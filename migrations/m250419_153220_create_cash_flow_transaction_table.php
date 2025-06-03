<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cash_flow_transaction}}`.
 */
class m250419_153220_create_cash_flow_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cash_flow_transaction}}', [
            'id' => $this->primaryKey(),
            'created' => $this->datetime()->defaultValue(null),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'comments' => $this->text()->defaultValue(null),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'cash_flow_transaction_category_id' => $this->integer(),
            'account_type_id' => $this->integer(),
            'creator_id' => $this->integer()->defaultValue(null),
            'modified' => $this->datetime()->defaultValue(null),
            'modifier_id' => $this->integer()->defaultValue(null),
            'amount' => $this->integer()->defaultValue(0),
        ]);

        $this->addCommentOnTable('{{%cash_flow_transaction}}', 'ДДС');

        // create index cash_flow_transaction_category_id
//        $this->createIndex(
//            '{{%idx-cash_flow_transaction-cash_flow_transaction_category_id}}',
//            '{{%cash_flow_transaction}}',
//            'cash_flow_transaction_category_id'
//        );

        // add foreign key for table `{{%cash_flow_transaction_category}}`
//        $this->addForeignKey(
//            '{{%fk-cash_flow_transaction-cash_flow_transaction_category_id}}',
//            '{{%cash_flow_transaction}}',
//            'cash_flow_transaction_category_id',
//            '{{%cash_flow_transaction_category}}',
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%cash_flow_transaction_category}}`
//        $this->dropForeignKey(
//            '{{%fk-cash_flow_transaction-cash_flow_transaction_category_id}}',
//            '{{%cash_flow_transaction}}'
//        );

        // drops index for column `type_id`
//        $this->dropIndex(
//            '{{%idx-cash_flow_transaction-cash_flow_transaction_category_id}}',
//            '{{%cash_flow_transaction}}'
//        );

        $this->dropTable('{{%cash_flow_transaction}}');
    }
}
