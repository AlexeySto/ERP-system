<?php

namespace app\controllers;

use app\models\BaseActiveRecord;
use app\models\Order;
use app\models\Product;
use app\widgets\Flashes;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\LoginForm;
use app\models\User;

class SiteController extends RefController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['$'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $userType = (int)Yii::$app->user->identity->type;
        // switch ($userType) {
            // case User::USER_TYPE_ADMIN:
                // return $this->redirect(['/orders/' . Order::TYPE_PUMP]);
            // case User::USER_TYPE_OWNER:
                // return $this->redirect(['/orders/' . Order::TYPE_PUMP]);
            // case User::USER_TYPE_SUPERVISOR:
                // return $this->redirect(['/orders/' . Order::TYPE_PUMP]);
            // case User::USER_TYPE_STOREKEEPER:
                // return $this->redirect(['/storage']);
        // }
        return $this->render('index');
    }

    /**
     * Displays profile page.
     *
     * @return string
     */
    public function actionProfile()
    {
        $profile = 'Профиль';
        if (Yii::$app->urlManager instanceof \app\web\UrlManager) {
            Yii::$app->urlManager->clear();
            Yii::$app->urlManager->addBreadcrumb($profile);
            Yii::$app->urlManager->addTitle($profile);
        }

        $model = Yii::$app->user->identity;
        $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flashes::setSuccess('Запись обновлена.');
            return $this->redirect2Referrer('', false);
        }

        return $this->render('profile', [
            'title' => $profile,
            'model' => $model,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
