<?php

namespace app\modules\rbac\migrations;
use Yii;
use yii\db\Migration;

abstract class BasePermissionsMigration extends Migration
{
    protected string $moduleId;         //kebab case
    protected string $controllerId;     //kebab case
    protected array $actions = [];    //kebab case ['action' => 'description']
    protected array $defaultActions = [
        'index' => 'Доступ к странице',
        'create' => 'Создавать записи на странице',
        'edit' => 'Редактировать записи на странице',
        'delete' => 'Удалять записи на странице',
    ];
    protected string $baseAction = '';  //action
    protected array $childrenMap = [];  //['action' => ['action1', 'action2']]

    abstract function configure();

    public function __construct()
    {
        parent::__construct();
        $this->configure();
        if (empty($this->moduleId)) {
            throw new \LogicException('Свойство $moduleId должно быть заполнено в классе ' . static::class);
        }
        if (empty($this->controllerId)) {
            throw new \LogicException('Свойство $controllerId должно быть заполнено в классе ' . static::class);
        }
        if (empty($this->actions)) {
            throw new \LogicException('Свойство $actionIds должно быть заполнено в классе ' . static::class);
        }
    }

    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        try {
            //add or get base permissions
            $basePermission = $this->createOrGetBasePermission($auth);
            if(!$basePermission) {
                if (empty($this->actions)) {
                    throw new \LogicException("Разрешение {$this->createPermissionName($this->baseAction)} не найдено, поэтому ключ {$this->baseAction} также должен быть также включен в \$actionIds" . static::class);
                }
            }
            //create permissions
            foreach ($this->actions as $actionId => $description) {
                $permissionName = $this->createPermissionName($actionId);
                $permission = $auth->getPermission($permissionName);
                if (!$permission) {
                    $permission = $auth->createPermission($permissionName);
                    $permission->description = $description;
                    $auth->add($permission);
                }
                if($basePermission && !$auth->hasChild($permission, $basePermission)) {
                    $auth->addChild($permission, $basePermission);
                }
            }
            //set permissions children
            foreach (($this->childrenMap ?? []) as $parent => $children) {
                $parentPermission = $auth->getPermission($this->createPermissionName($parent));
                if($parentPermission && is_array($children)) {
                    foreach ($children as $child) {
                        echo "$parent -> $child\r\n";
                        $childPermission = $auth->getPermission($this->createPermissionName($child));
                        if($childPermission && !$auth->hasChild($parentPermission, $childPermission)) {
                            $auth->addChild($parentPermission, $childPermission);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Yii::error("Ошибка при создании разрешений: {$e->getMessage()}");
            throw $e;
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        try {
            //remove children
            foreach (($this->childrenMap ?? []) as $parent => $children) {
                $parentPermission = $auth->getPermission($this->createPermissionName($parent));
                if($parentPermission && is_array($children)) {
                    foreach ($children as $child) {
                        $childPermission = $auth->getPermission($this->createPermissionName($child));
                        if($childPermission && $auth->hasChild($parentPermission, $childPermission)) {
                            $auth->removeChild($parentPermission, $childPermission);
                        }
                    }
                }
            }
            //remove permissions
            foreach (array_keys($this->actions ?? []) as $actionId) {
                $permission = $auth->getPermission($this->createPermissionName($actionId));
                if ($permission) {
                    $auth->remove($permission);
                }
            }
        } catch (\Exception $e) {
            Yii::error("Ошибка при удалении разрешений: {$e->getMessage()}", __METHOD__);
            throw new \Exception("Не удалось выполнить откат миграции: {$e->getMessage()}", 0, $e);
        }
    }

    private function createPermissionName($action): string
    {
        return implode('_', [$this->moduleId, $this->controllerId, $action]);
    }

    private function createOrGetBasePermission($auth)
    {
        if (!$this->baseAction) {
            return null;
        }
        $name = $this->createPermissionName($this->baseAction);
        $basePermission = $auth->getPermission($name);
        if (array_key_exists($this->baseAction, $this->actions)) {
            if(!$basePermission) {
                $basePermission = $auth->createPermission($name);
                $basePermission->description = $this->actions[$this->baseAction];
                $auth->add($basePermission);
            } else {
                $basePermission->description = $this->actions[$this->baseAction];
                $auth->update($name, $basePermission);
            }
            unset($this->actions[$this->baseAction]);
        }
        return $basePermission;
    }
}
