<?php


namespace app\modules\rbac;


use Yii;
use yii\helpers\ArrayHelper;

use yii\rbac\DbManager;

class AccessChecker extends DbManager
{
    public function checkAccess($userId, $permissionName, $params = [])
    {
        if(self::checkFullAccess($userId)) {
            return true;
        }
        return parent::checkAccess($userId, $permissionName, $params);
    }

    public static function checkFullAccess($userId) {
        $fullAccessIds = Yii::$app->getModule('rbac')->fullAccess['userIds'] ?? false;
        if($fullAccessIds) {
            foreach ($fullAccessIds as $id) {
                if($userId === $id) {
                    return true;
                }
            }
        }

        $fullAccessRoles = Yii::$app->getModule('rbac')->fullAccess['roles'] ?? false;
        if($fullAccessRoles) {
            foreach (ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($userId), 'name') as $item) {
                if(in_array($item, $fullAccessRoles)) {
                    return true;
                }
            }
        }
    }
}