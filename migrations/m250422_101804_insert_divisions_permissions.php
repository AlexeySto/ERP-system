<?php

use app\modules\rbac\migrations\BasePermissionsMigration;

class m250422_101804_insert_divisions_permissions extends BasePermissionsMigration
{
    function configure()
    {
        $this->moduleId = 'app';
        $this->controllerId = 'divisions';
        $this->actions = $this->defaultActions;
        $this->baseAction = 'index';
        $this->childrenMap = [
            'create' => [
                'edit'
            ]
        ];
    }
}
