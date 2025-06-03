<?php

use yii\db\Migration;

/**
 * Class m250213_193427_insert_some_cash_flow_transaction_kinds
 */
class m250213_193427_insert_some_cash_flow_transaction_kinds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->batchInsert(
			'cash_flow_transaction_kind',
            ['name', 'short_name'],
            [
				['Приход', 'прих.'],
				['Расход', 'расх.'],
			]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250213_193427_insert_some_cash_flow_transaction_kinds cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250213_193427_insert_some_cash_flow_transaction_kinds cannot be reverted.\n";

        return false;
    }
    */
}
