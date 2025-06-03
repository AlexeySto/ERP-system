<?php
use app\modules\rbac\migrations\BasePermissionsMigration;

class m250511_121021_insert_dict_object_score_sections_premissions extends BasePermissionsMigration
{
    function configure()
    {
        $this->moduleId = 'app';
        $this->controllerId = 'object-score-sections';
        $this->actions = $this->defaultActions;
        $this->baseAction = 'index';
        $this->childrenMap = [
            'create' => [
                'edit'
            ]
        ];
    }
}
