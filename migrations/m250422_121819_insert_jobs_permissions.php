<?php

use app\modules\rbac\migrations\BasePermissionsMigration;
use yii\db\Migration;

class m250422_121819_insert_jobs_permissions extends BasePermissionsMigration
{
    function configure()
    {
        $this->moduleId = 'app';
        $this->controllerId = 'jobs';
        $this->actions = $this->defaultActions;
        $this->baseAction = 'index';
        $this->childrenMap = [
            'create' => [
                'edit'
            ]
        ];
    }
}
