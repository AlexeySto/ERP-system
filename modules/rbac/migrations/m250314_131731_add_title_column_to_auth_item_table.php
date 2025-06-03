<?php

namespace app\modules\rbac\migrations;

use Yii;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%auth_item}}`.
 */
class m250314_131731_add_title_column_to_auth_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%auth_item}}', 'title', $this->string(64)->after('type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%auth_item}}', 'title');
    }
}
