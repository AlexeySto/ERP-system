<?php


namespace app\modules\rbac\models;


use app\modules\rbac\AccessChecker;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;

class RbacAccessControl extends AccessControl
{
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        $user = $this->user;
        if(AccessChecker::checkFullAccess($user->id)) {
            return true;
        }
        $request = Yii::$app->getRequest();
        /* @var $rule AccessRule */
        foreach ($this->rules as $rule) {
            if ($allow = $rule->allows($action, $user, $request)) {
                return true;
            } elseif ($allow === false) {
                if (isset($rule->denyCallback)) {
                    call_user_func($rule->denyCallback, $rule, $action);
                } elseif ($this->denyCallback !== null) {
                    call_user_func($this->denyCallback, $rule, $action);
                } else {
                    $this->denyAccess($user);
                }
                return false;
            }
        }
        $permission_name = implode('_', [$action->controller->module->id, $action->controller->id, $action->id]);
        if(Yii::$app->authManager->getPermission($permission_name)) {
            if (\Yii::$app->user->can($permission_name)) {
                return true;
            } else {
                $this->denyAccess($user);
            }
        }
        if ($this->denyCallback !== null) {
            call_user_func($this->denyCallback, null, $action);
        } else {
            $this->denyAccess($user);
        }
        return false;
    }
}