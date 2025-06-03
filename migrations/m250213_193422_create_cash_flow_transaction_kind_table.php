<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cash_flow_transaction_kind}}`.
 */
class m250213_193422_create_cash_flow_transaction_kind_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cash_flow_transaction_kind}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull(),
            'short_name' => $this->string(16)->notNull(),
            'comments' => $this->text()->defaultValue(null),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
		
		$this->alterColumn('{{%cash_flow_transaction_kind}}', 'id', $this->tinyInteger().' NOT NULL AUTO_INCREMENT');
		
		$this->addCommentOnTable('{{%cash_flow_transaction_kind}}', 'Относится ли категория транзакции к зарплатам. Это служебная таблица - важна для логики работы приложения');
		
		$this->addCommentOnColumn('{{%cash_flow_transaction_kind}}', 'comments', 'Любые комментарии');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cash_flow_transaction_kind}}');
    }
}
