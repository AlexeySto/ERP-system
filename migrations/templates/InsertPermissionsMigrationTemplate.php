<?php

namespace app\migrations\templates;

use Yii;
use yii\db\Migration;

class InsertPermissionsMigrationTemplate extends Migration
{
    protected $permissions = [
        'app_url_index' => 'Доступ к странице',
        'app_url_view' => 'Просмотр отдельных записей',
        'app_url_create' => 'Создавать записи на странице',
        'app_url_edit' => 'Редактировать записи на странице',
        'app_url_delete' => 'Удалять записи на странице',
    ];
    protected $basePermissionName = 'app_url_index';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if (array_key_exists($this->basePermissionName, $this->permissions)) {
            $basePermission = $auth->createPermission($this->basePermissionName);
            $basePermission->description = $this->permissions[$this->basePermissionName];
            $auth->add($basePermission);
            unset($this->permissions[$this->basePermissionName]);
        } else {
            $basePermission = $auth->getPermission($this->basePermissionName);
        }

        foreach ($this->permissions as $name => $description) {
            $permission = $auth->createPermission($name);
            $permission->description = $description;
            $auth->add($permission);
            if ($basePermission) {
                $auth->addChild($permission, $basePermission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach (array_keys($this->permissions) as $name) {
            $auth = Yii::$app->authManager;
            if ($permission = $auth->getPermission($name)) {
                Yii::$app->authManager->remove($permission);
            }
        }
    }
}
