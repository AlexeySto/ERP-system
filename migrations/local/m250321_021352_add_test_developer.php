<?php

use yii\db\Migration;
use app\models\AuthUser;

class m250321_021352_add_test_developer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $developer = new AuthUser([
            'firstname'         => 'Тестовый',
            'patronym'          => 'Разработчик',
            'lastname'          => 'Разработчик',
            'fullname'          => 'Разработчик',
            'fio'               => 'Иванов И.И.',
            'email'             => 'developer@test.ru',
            'user_status_id'    => 1,
        ]);
        $developer->setPassword('developer');
        $developer->generateAuthKey();
        if($developer->save()) {
            $role = Yii::$app->authManager->getRole('Developer');
            Yii::$app->authManager->assign($role, $developer);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return AuthUser::deleteAll(['email' => 'developer@test.ru']);
    }
}
