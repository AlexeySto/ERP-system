<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%division}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m250208_213655_create_division_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%division}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'comments' => $this->text()->defaultValue(null),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created' => $this->datetime()->defaultValue(null),
            'creator_id' => $this->integer()->defaultValue(null),
            'modified' => $this->datetime()->defaultValue(null),
            'modifier_id' => $this->integer()->defaultValue(null),
        ]);

		$this->addCommentOnTable('{{%division}}', 'Company division (подразделение компании)');
		
		$this->addCommentOnColumn('{{%division}}', 'comments', 'Любые комментарии');

        // creates index for column `creator_id`
        $this->createIndex(
            '{{%idx-division-creator_id}}',
            '{{%division}}',
            'creator_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-division-creator_id}}',
            '{{%division}}',
            'creator_id',
            '{{%user}}',
            'id',
            'SET NULL', // Чтобы не удалилась запись
            'CASCADE'
        );

        // creates index for column `modifier_id`
        $this->createIndex(
            '{{%idx-division-modifier_id}}',
            '{{%division}}',
            'modifier_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-division-modifier_id}}',
            '{{%division}}',
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
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-division-creator_id}}',
            '{{%division}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-division-creator_id}}',
            '{{%division}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-division-modifier_id}}',
            '{{%division}}'
        );

        // drops index for column `modifier_id`
        $this->dropIndex(
            '{{%idx-division-modifier_id}}',
            '{{%division}}'
        );

        $this->dropTable('{{%division}}');
    }
}
