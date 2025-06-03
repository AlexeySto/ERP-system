<?php

namespace app\modules\rbac\controllers;

use app\controllers\BaseController;
use app\modules\rbac\models\AuthAccess;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Warehouses Controller
 */
class DefaultController extends BaseController
{
    public $accessRules = [
        [
            'actions' => ['create-role', 'update-role', 'delete-role', 'switch-permission'],
            'allow' => true,
            'roles' => ['rbac_default_index']
        ]
    ];

    public function actionIndex()
    {
        $dataProvider = new ArrayDataProvider(['allModels' => AuthAccess::getAccessDataSet()]);
        $dataProvider->setPagination(false);
        return $this->render('index', [
            'roles' => AuthAccess::getRoles(),
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreateRole()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $post = \Yii::$app->request->post();
        $result = AuthAccess::createRole($post['title'], $post['description']);
        if($result) {
            AuthAccess::copyRolePermissions($post['from_role_name'], $result);
        }
        return ['result' => $result];
    }

    public function actionUpdateRole()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();
        $result = AuthAccess::updateRole($post['name'], $post['title'], $post['description'], true);
        if($result) {
            AuthAccess::copyRolePermissions($post['from_role_name'], $result);
        }
        return ['result' => (bool)$result];
    }

    public function actionDeleteRole()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $roleName = \Yii::$app->request->post('name');
        $users = AuthAccess::getRoleUsers($roleName);
        if(empty($users)) {
            \Yii::$app->authManager->remove(\Yii::$app->authManager->getRole($roleName));
        }
        return [
            'result' => empty($users),
            'users' => $users
        ];
    }

    public function actionSwitchPermission()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $role = \Yii::$app->request->post('role');
        $permission = \Yii::$app->request->post('permission');
        $value = \Yii::$app->request->post('value');
        return [
            'result' => AuthAccess::switchRole($role, $permission, $value),
        ];
    }
}
