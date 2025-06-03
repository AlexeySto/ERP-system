<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cash_flow_transaction_category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%cash_flow_transaction_type}}`
 * - `{{%cash_flow_transaction_kind}}`
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m250213_193442_create_cash_flow_transaction_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cash_flow_transaction_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'comments' => $this->text()->defaultValue(null),
            'type_id' => $this->tinyInteger()->notNull(),
            'kind_id' => $this->tinyInteger()->defaultValue(null),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created' => $this->datetime()->defaultValue(null),
            'creator_id' => $this->integer()->defaultValue(null),
            'modified' => $this->datetime()->defaultValue(null),
            'modifier_id' => $this->integer()->defaultValue(null),
        ]);

		$this->addCommentOnTable('{{%cash_flow_transaction_category}}', 'Категория ДДС');
		
		$this->addCommentOnColumn('{{%cash_flow_transaction_category}}', 'comments', 'Любые комментарии');

        // creates index for column `type_id`
        $this->createIndex(
            '{{%idx-cash_flow_transaction_category-type_id}}',
            '{{%cash_flow_transaction_category}}',
            'type_id'
        );

        // add foreign key for table `{{%cash_flow_transaction_type}}`
        $this->addForeignKey(
            '{{%fk-cash_flow_transaction_category-type_id}}',
            '{{%cash_flow_transaction_category}}',
            'type_id',
            '{{%cash_flow_transaction_type}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `kind_id`
        $this->createIndex(
            '{{%idx-cash_flow_transaction_category-kind_id}}',
            '{{%cash_flow_transaction_category}}',
            'kind_id'
        );

        // add foreign key for table `{{%cash_flow_transaction_kind}}`
        $this->addForeignKey(
            '{{%fk-cash_flow_transaction_category-kind_id}}',
            '{{%cash_flow_transaction_category}}',
            'kind_id',
            '{{%cash_flow_transaction_kind}}',
            'id',
			'SET NULL', // Чтобы не удалилась запись
            'CASCADE'
        );

        // creates index for column `creator_id`
        $this->createIndex(
            '{{%idx-cash_flow_transaction_category-creator_id}}',
            '{{%cash_flow_transaction_category}}',
            'creator_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-cash_flow_transaction_category-creator_id}}',
            '{{%cash_flow_transaction_category}}',
            'creator_id',
            '{{%user}}',
            'id',
			'SET NULL', // Чтобы не удалилась запись
            'CASCADE'
        );

        // creates index for column `modifier_id`
        $this->createIndex(
            '{{%idx-cash_flow_transaction_category-modifier_id}}',
            '{{%cash_flow_transaction_category}}',
            'modifier_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-cash_flow_transaction_category-modifier_id}}',
            '{{%cash_flow_transaction_category}}',
            'modifier_id',
            '{{%user}}',
            'id',
			'SET NULL', // Чтобы не удалилась запись
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%cash_flow_transaction_type}}`
        $this->dropForeignKey(
            '{{%fk-cash_flow_transaction_category-type_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops index for column `type_id`
        $this->dropIndex(
            '{{%idx-cash_flow_transaction_category-type_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops foreign key for table `{{%cash_flow_transaction_kind}}`
        $this->dropForeignKey(
            '{{%fk-cash_flow_transaction_category-kind_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops index for column `kind_id`
        $this->dropIndex(
            '{{%idx-cash_flow_transaction_category-kind_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-cash_flow_transaction_category-creator_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-cash_flow_transaction_category-creator_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-cash_flow_transaction_category-modifier_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        // drops index for column `modifier_id`
        $this->dropIndex(
            '{{%idx-cash_flow_transaction_category-modifier_id}}',
            '{{%cash_flow_transaction_category}}'
        );

        $this->dropTable('{{%cash_flow_transaction_category}}');
    }
}
