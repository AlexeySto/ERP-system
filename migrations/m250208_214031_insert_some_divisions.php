<?php

use yii\db\Migration;

/**
 * Class m250208_214031_insert_some_divisions
 */
class m250208_214031_insert_some_divisions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->batchInsert(
			'division',
            ['name', 'comments', 'created', 'creator_id'],
            [
				['Топ-менеджмент', 'Высшее руководство компании', date('Y-m-d H:i:s'), 1],
				['Маркетинг', null, date('Y-m-d H:i:s'), 1],
				['Продажи', null, date('Y-m-d H:i:s'), 1],
				['Бухгалтерия', null, date('Y-m-d H:i:s'), 1],
				['Производство', null, date('Y-m-d H:i:s'), 1],
				['Склад', null, date('Y-m-d H:i:s'), 1],
				['Логистика', null, date('Y-m-d H:i:s'), 1],
			]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250208_214031_insert_some_divisions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250208_214031_insert_some_divisions cannot be reverted.\n";

        return false;
    }
    */
}
