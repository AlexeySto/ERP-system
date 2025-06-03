<?php

use app\modules\rbac\migrations\BasePermissionsMigration;

class m250515_095041_insert_cash_flow_summary_permissions extends BasePermissionsMigration
{
    function configure()
    {
        $this->moduleId = 'app';
        $this->controllerId = 'cash-flow-summary';
        $this->actions = [
            'index' => 'Доступ к странице',
        ];
        $this->baseAction = 'index';
    }
}
