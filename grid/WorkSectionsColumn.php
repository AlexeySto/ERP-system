<?php

namespace app\grid;

use yii\grid\DataColumn;

class WorkSectionsColumn extends DataColumn
{
    public $format = 'raw';

    public $dateFormat      = '';
    public $userAttribute   = '';
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        return $this->render;
        $value = $model->__get($this->attribute);
        if($this->dateFormat) {
            $value = \Yii::$app->formatter->format($value, $this->dateFormat);
        }

        if(!$this->userAttribute) return $value;

        $userId = $model->__get($this->userAttribute);
        if(empty($userId)) return $value;

        if(!isset(self::$cache[$userId]))
        {
            $userModel = User::findOne($userId);
            if(!$userModel) return '-';
            self::$cache[$userId] = $userModel->getFullName();
        }
        return $value . ' (' . self::$cache[$userId] . ')';
    }
}
