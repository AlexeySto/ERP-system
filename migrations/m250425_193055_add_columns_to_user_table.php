<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m250425_193055_add_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'state_id', $this->integer()->notNull()->defaultValue(1));
        $this->addColumn('{{%user}}', 'have_account', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'state_id');
        $this->dropColumn('{{%user}}', 'have_account');
    }
}
