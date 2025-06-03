<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DevController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionRemovePermission($name)
    {
        $auth = \Yii::$app->authManager;
        $item = $auth->getPermission($name);
        if($item) {
            if($auth->remove($item)) {
                echo "Permission $name was removed";
            } else {
                echo "Permission $name remove error";
            }
        } else {
            echo "Permission $name not found";
        }
        echo "\r\n";
        return ExitCode::OK;
    }

    public function actionFixRoles()
    {
        $auth = \Yii::$app->authManager;
        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getChildRoles($role->name) as $childRole) {
                $auth->removeChild($role, $childRole);
            }
        }
    }

    public function actionRenameRole($from, $to)
    {
        $auth = \Yii::$app->authManager;
        if($role = $auth->getRole($from)) {
            $role->name = $to;
            $auth->update($from, $role);
        }
    }

    public function actionRenamePermission($from, $to)
    {
        $auth = \Yii::$app->authManager;
        if($permission = $auth->getPermission($from)) {
            $permission->name = $to;
            $auth->update($from, $permission);
        }
    }
}
