<?php

namespace app\modules\rbac\migrations;

use Yii;
use yii\db\Migration;

class m250317_224130_insert_rbac_module_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('rbac_default_index');
        $permission->description = 'Доступ к управлению правами';
        $auth->add($permission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('rbac_default_index_Read');
        $auth->remove($permission);
        return true;
    }
}
