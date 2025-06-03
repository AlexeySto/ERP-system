<?php

use yii\db\Migration;

/**
 * Class m250213_193409_insert_some_cash_flow_transaction_types
 */
class m250213_193409_insert_some_cash_flow_transaction_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->batchInsert(
			'cash_flow_transaction_type',
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
        echo "m250213_193409_insert_some_cash_flow_transaction_types cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250213_193409_insert_some_cash_flow_transaction_types cannot be reverted.\n";

        return false;
    }
    */
}
