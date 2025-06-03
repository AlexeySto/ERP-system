<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%job}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%job_type}}`
 * - `{{%division}}`
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m250208_214008_create_job_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%job}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'comments' => $this->text()->defaultValue(null),
            'type_id' => $this->smallInteger(),
            'division_id' => $this->integer(),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created' => $this->datetime()->defaultValue(null),
            'creator_id' => $this->integer()->defaultValue(null),
            'modified' => $this->datetime()->defaultValue(null),
            'modifier_id' => $this->integer()->defaultValue(null),
        ]);

		$this->addCommentOnTable('{{%job}}', 'Должность. Существует в рамках подразделения. В другом подразделении может быть должность с тем же названием.');
		$this->addCommentOnColumn('{{%job}}', 'comments', 'Любые комментарии');

        // creates index for column `type_id`
        $this->createIndex(
            '{{%idx-job-type_id}}',
            '{{%job}}',
            'type_id'
        );

        // add foreign key for table `{{%job_type}}`
        $this->addForeignKey(
            '{{%fk-job-type_id}}',
            '{{%job}}',
            'type_id',
            '{{%job_type}}',
            'id',
            'SET NULL', // Чтобы не удалилась запись
            'CASCADE'
        );

		// creates index for column `division_id`
        $this->createIndex(
            '{{%idx-job-division_id}}',
            '{{%job}}',
            'division_id'
        );

        // add foreign key for table `{{%division}}`
        $this->addForeignKey(
            '{{%fk-job-division_id}}',
            '{{%job}}',
            'division_id',
            '{{%division}}',
            'id',
            'CASCADE', // On delete cascade, потому что должность без подразделения (division) не имеет смысла.
            'CASCADE'
        );

        // creates index for column `creator_id`
        $this->createIndex(
            '{{%idx-job-creator_id}}',
            '{{%job}}',
            'creator_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-job-creator_id}}',
            '{{%job}}',
            'creator_id',
            '{{%user}}',
            'id',
			'SET NULL', // Чтобы не удалилась запись
            'CASCADE'
        );

        // creates index for column `modifier_id`
        $this->createIndex(
            '{{%idx-job-modifier_id}}',
            '{{%job}}',
            'modifier_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-job-modifier_id}}',
            '{{%job}}',
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
        // drops foreign key for table `{{%job_type}}`
        $this->dropForeignKey(
            '{{%fk-job-type_id}}',
            '{{%job}}'
        );

        // drops index for column `type_id`
        $this->dropIndex(
            '{{%idx-job-type_id}}',
            '{{%job}}'
        );
		
		// drops foreign key for table `{{%division}}`
        $this->dropForeignKey(
            '{{%fk-job-division_id}}',
            '{{%job}}'
        );

        // drops index for column `division_id`
        $this->dropIndex(
            '{{%idx-job-division_id}}',
            '{{%job}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-job-creator_id}}',
            '{{%job}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-job-creator_id}}',
            '{{%job}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-job-modifier_id}}',
            '{{%job}}'
        );

        // drops index for column `modifier_id`
        $this->dropIndex(
            '{{%idx-job-modifier_id}}',
            '{{%job}}'
        );

        $this->dropTable('{{%job}}');
    }
}
