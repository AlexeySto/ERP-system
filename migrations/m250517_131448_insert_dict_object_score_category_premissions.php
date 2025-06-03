<?php

use app\modules\rbac\migrations\BasePermissionsMigration;

class m250517_131448_insert_dict_object_score_category_premissions extends BasePermissionsMigration
{
       function configure()
    {
        $this->moduleId = 'app';
        $this->controllerId = 'object-score-category';
        $this->actions = $this->defaultActions;
        $this->baseAction = 'index';
        $this->childrenMap = [
            'create' => [
                'edit'
            ]
        ];
    }
}
