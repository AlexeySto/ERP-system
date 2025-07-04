<?php

namespace app\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\ErrorException;
use yii\db\Exception;

/**
 * Class Contacts
 */
class ObjectExtBehavior extends Behavior
{
    /**
     * Stores a list of relations, affected by the behavior. Configurable property.
     * @var array
     */
    public $relations = [];

    /**
     * Stores values of relation attributes.
     * old attributes and will be loaded in loadRelations().
     * @var array
     */
    private $_oldValues = []; // Changes

    /**
     * Stores values of relation attributes. All entries in this array are considered
     * dirty (changed) attributes and will be saved in saveRelations().
     * @var array
     */
    private $_values = [];

    /**
     * Used to store fields that this behavior creates. Each field refers to a relation
     * and has optional getters and setters.
     * @var array
     */
    private $_fields = [];

    /**
     * Events list
     * @return array
     */
    public function events()
    {
        return [
//            ActiveRecord::EVENT_AFTER_FIND   => 'loadRelations',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveRelations',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveRelations',
        ];
    }

    protected $_initialized = [];

    public function initRelationData($attributeName)
    {
        if (isset($this->_initialized[$attributeName])) return;

        $params = $this->relations[$attributeName] ?? null;
        if (is_null($params)) return;

        $loadedData = $this->loadData($attributeName);
        $this->_values[$attributeName] = $this->_oldValues[$attributeName] = $loadedData;
        $this->_initialized[$attributeName] = true;
    }

    /**
     * Invokes init of parent class and assigns proper values to internal _fields variable
     */
    public function init()
    {
        parent::init();
        foreach ($this->relations as $attributeName => $params) {
            $this->_fields[$attributeName] = [
                'attribute' => $attributeName,
                'class'     => $params[0],
                'key'       => $params[1],
                'fields'    => $params[2],
                'totals'    => $params[3] ?? [],
                'callback'  => $params[4] ?? null,
            ];
        }
    }

    public function getChanges() // Changes
    {
        $retData = ['new' => [], 'old' => []];
        foreach ($this->relations as $attributeName => $params)
        {
            if (!$this->hasNewValue($attributeName)) {
                continue;
            }

            $oldValue = $this->getOldValue($attributeName);
            //$oldValue = isset($this->_oldValues[$attributeName]) && is_array($this->_oldValues[$attributeName]) ? $this->_oldValues[$attributeName] : [];
            $newValue = isset($this->_values[$attributeName]) && is_array($this->_values[$attributeName]) ? $this->_values[$attributeName] : [];

            $fieldParams = $this->getFieldParams($attributeName);
            $keyName = $fieldParams['key'];
            $fields  = $fieldParams['fields'];
            //$className = $fieldParams['class'];


            // Cleanup
            foreach($oldValue as $id => $data) {
                foreach($data as $k => $v) {
                    //if(in_array($k, ["id", "created", "creator_id", "modified", "modifier_id", $keyName])) unset($oldValue[$id][$k]);
                    //if($k == 'status' && !isset($newValue[$k])) unset($oldValue[$id][$k]);
                    if(!in_array($k, $fields)) unset($oldValue[$id][$k]);
                }
            }

            $diff = false;
            foreach($oldValue as $id => $data) {
                if(!isset($newValue[$id])) {
                    $diff = true;
                    break;
                }
                if(sizeof(array_diff_assoc($data, $newValue[$id]))) {
                    $diff = true;
                    break;
                }
            }
            foreach($newValue as $id => $data) {
                if(!isset($oldValue[$id])) {
                    $diff = true;
                    break;
                }
            }
            if($diff)
            {
                $retData['new'][$attributeName] = $newValue;
                $retData['old'][$attributeName] = $oldValue;
            }
        }
        return $retData;
    }

//    /**
//     * Save all dirty (changed) relation values ($this->_values) to the database
//     * @param $event
//     * @throws ErrorException
//     * @throws Exception
//     */
//    public function loadRelations($event)
//    {
//        /**
//         * @var $primaryModel \yii\db\ActiveRecord
//         */
//        $primaryModel = $this->owner;
//
//        if (is_array($primaryModelPk = $primaryModel->getPrimaryKey())) {
//            throw new ErrorException("This behavior does not support composite primary keys");
//        }
//
//        // load relations data
//        foreach ($this->relations as $attributeName => $params) {
//            $this->_values[$attributeName] = $this->_oldValues[$attributeName] = $this->loadData($attributeName);
//        }
//    }

    protected $_last_result = true;
    public $totals = [];

    public function isCorrectlySaved()
    {
        return $this->_last_result;
    }

    static protected $_executors = [];

    /**
     * Save all dirty (changed) relation values ($this->_values) to the database
     * @param $event
     * @throws ErrorException
     * @throws Exception
     */
    public function saveRelations($event)
    {
        /**
         * @var $primaryModel \yii\db\ActiveRecord
         */
        $primaryModel = $this->owner;
        $this->_last_result = true;

        if (is_array($primaryModelPk = $primaryModel->getPrimaryKey())) {
            throw new ErrorException("This behavior does not support composite primary keys");
        }

        $executor = get_class($primaryModel) . '.' . $primaryModelPk;
        if(isset(static::$_executors[$executor])) return;
        static::$_executors[$executor] = true;

        $connection = $primaryModel::getDb();
        $transaction = $connection->beginTransaction();
        $result = true;

        foreach ($this->_fields as $attributeName => $params) {
            if (!$this->hasNewValue($attributeName)) {
                continue;
            }

            $className = $params['class'];
            $keyName = $params['key'];
            $fields  = $params['fields'];
            $totals  = $params['totals'];
            $callback = $params['callback'];

            $oldValue = $this->getOldValue($attributeName);
            $newValue = $this->getNewValue($attributeName);
            $exists = [];
            foreach ($newValue as $newId => $newData) {
                // Update existing records
                if(is_numeric($newId)) {
                    $model = $className::findOne($newId);
                    $exists[$newId] = true;
                    if ($model->__get($keyName) == 0) {
                        $model->__set($keyName, $primaryModelPk);
                    }
                }
                // Add new records
                else
                {
                    $model = new $className();
                    $model->__set($keyName, $primaryModelPk);
                }
                $scenarios = $model->scenarios();
                if (isset($scenarios[$primaryModel->scenario])) $model->scenario = $primaryModel->scenario;
                foreach($newData as $key => $value) {
                    if(!in_array($key, $fields)) continue;
                    $model->__set($key, $value);
                    if (is_array($totals) && isset($totals[$key])) {
                        if (!isset($this->totals[$key])) $this->totals[$key] = 0;
                        $this->totals[$key] += number_format($value, $totals[$key], '.', '');
                    }
                }
                if(!$model->save()) {
                    $result = false;
                    break;
                }
            }

            foreach($oldValue as $oldId => $oldData) {
                if(isset($exists[$oldId])) continue;
                $model = $className::findOne($oldId);
                if(!$model->delete()) {
                    $result = false;
                    break;
                }
            }

            if ($result && $callback && ($callback instanceof \Closure)) {
                if (!$callback($primaryModel, $this->totals)) {
                    $result = false;
                }
            }
        }

        $this->_last_result = $result;

        if($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
    }

    /**
     * Check if an attribute is dirty and must be saved (its new value exists)
     * @param string $attributeName
     * @return null
     */
    private function hasNewValue($attributeName)
    {
        return isset($this->_values[$attributeName]);
    }

    /**
     * Get value of a dirty attribute by name
     * @param string $attributeName
     * @return null
     */
    private function getNewValue($attributeName)
    {
        return isset($this->_values[$attributeName]) && is_array($this->_values[$attributeName]) ? $this->_values[$attributeName] : [];
    }

    /**
     * Get initialized value
     * @param string $attributeName
     * @return null
     */
    private function getOldValue($attributeName)
    {
        $this->initRelationData($attributeName);
        return isset($this->_oldValues[$attributeName]) && is_array($this->_oldValues[$attributeName]) ? $this->_oldValues[$attributeName] : [];
    }

    /**
     * Get parameters of a field
     * @param string $fieldName
     * @return mixed
     * @throws ErrorException
     */
    private function getFieldParams($fieldName)
    {
        if (empty($this->_fields[$fieldName])) {
            throw new ErrorException("Parameter \"{$fieldName}\" does not exist");
        }

        return $this->_fields[$fieldName];
    }

    /**
     * Returns a value indicating whether a property can be read.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     * TODO: Make it honor $checkVars ??
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return array_key_exists($name, $this->_fields) ?
            true : parent::canGetProperty($name, $checkVars);
    }

    /**
     * Returns a value indicating whether a property can be set.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     * TODO: Make it honor $checkVars and $checkBehaviors ??
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @param boolean $checkBehaviors whether to treat behaviors' properties as properties of this component
     * @return boolean whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return array_key_exists($name, $this->_fields) ?
            true : parent::canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * Returns the value of an object property.
     * Get it from our local temporary variable if we have it,
     * get if from DB otherwise.
     *
     * @param string $name the property name
     * @return mixed the property value
     * @see __set()
     */
    public function __get($name)
    {
        $this->initRelationData($name);
        if ($this->hasNewValue($name)) {
            $value = $this->getNewValue($name);
        } else {
            $value = $this->getOldValue($name);
        }
        return $value;
    }

    protected function loadData($name)
    {
        $fieldParams = $this->getFieldParams($name);

        $className = $fieldParams['class'];
        $keyName = $fieldParams['key'];

        $primaryModel = $this->owner;

        if (is_array($primaryModelPk = $primaryModel->getPrimaryKey())) {
            throw new ErrorException("This behavior does not support composite primary keys");
        }

        if (method_exists($className, 'findActive'))
            $qry = $className::findActive();
        else
            $qry = $className::find();

        $qry->andWhere([$keyName => $primaryModelPk])->indexBy('id')->orderBy(['id' => SORT_ASC]);

        return $qry->asArray(TRUE)->all();
    }

    /**
     * Sets the value of a component property. The data is passed
     *
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @see __get()
     */
    public function __set($name, $value)
    {
        //$fieldParams = $this->getFieldParams($name);
        //$attributeName = $fieldParams['attribute'];
        //$this->_values[$attributeName] = $value;
        $this->initRelationData($name);
        $this->_values[$name] = $value;
    }
}

