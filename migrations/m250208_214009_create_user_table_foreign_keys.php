<?php

use yii\db\Migration;

/**
 * Handles the creation of foreign keys for table `{{%user}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%job}}`
 * - `{{%user}}`
 * - `{{%user_status}}`
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m250208_214009_create_user_table_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = $this->db->tablePrefix . 'user';
		if ($this->db->getTableSchema($tableName, true) !== null) {
			// creates index for column `job_id`
			$this->createIndex(
				'{{%idx-user-job_id}}',
				'{{%user}}',
				'job_id'
			);

			// add foreign key for table `{{%job}}`
			$this->addForeignKey(
				'{{%fk-user-job_id}}',
				'{{%user}}',
				'job_id',
				'{{%job}}',
				'id',
				'SET NULL', // Чтобы не удалилась запись
				'CASCADE'
			);

			// creates index for column `superior_id`
			$this->createIndex(
				'{{%idx-user-superior_id}}',
				'{{%user}}',
				'superior_id'
			);

			// add foreign key for table `{{%user}}`
			$this->addForeignKey(
				'{{%fk-user-superior_id}}',
				'{{%user}}',
				'superior_id',
				'{{%user}}',
				'id',
				'SET NULL', // Чтобы не удалилась запись
				'CASCADE'
			);

			// creates index for column `user_status_id`
			$this->createIndex(
				'{{%idx-user-user_status_id}}',
				'{{%user}}',
				'user_status_id'
			);

			// add foreign key for table `{{%user_status}}`
			$this->addForeignKey(
				'{{%fk-user-user_status_id}}',
				'{{%user}}',
				'user_status_id',
				'{{%user_status}}',
				'id',
				'CASCADE',
				'CASCADE'
			);

			// creates index for column `creator_id`
			$this->createIndex(
				'{{%idx-user-creator_id}}',
				'{{%user}}',
				'creator_id'
			);

			// add foreign key for table `{{%user}}`
			$this->addForeignKey(
				'{{%fk-user-creator_id}}',
				'{{%user}}',
				'creator_id',
				'{{%user}}',
				'id',
				'SET NULL', // Чтобы не удалилась запись
				'CASCADE'
			);

			// creates index for column `modifier_id`
			$this->createIndex(
				'{{%idx-user-modifier_id}}',
				'{{%user}}',
				'modifier_id'
			);

			// add foreign key for table `{{%user}}`
			$this->addForeignKey(
				'{{%fk-user-modifier_id}}',
				'{{%user}}',
				'modifier_id',
				'{{%user}}',
				'id',
				'SET NULL', // Чтобы не удалилась запись
				'CASCADE'
			);
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%job}}`
        $this->dropForeignKey(
            '{{%fk-user-job_id}}',
            '{{%user}}'
        );

        // drops index for column `job_id`
        $this->dropIndex(
            '{{%idx-user-job_id}}',
            '{{%user}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user-superior_id}}',
            '{{%user}}'
        );

        // drops index for column `superior_id`
        $this->dropIndex(
            '{{%idx-user-superior_id}}',
            '{{%user}}'
        );

        // drops foreign key for table `{{%user_status}}`
        $this->dropForeignKey(
            '{{%fk-user-user_status_id}}',
            '{{%user}}'
        );

        // drops index for column `user_status_id`
        $this->dropIndex(
            '{{%idx-user-user_status_id}}',
            '{{%user}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user-creator_id}}',
            '{{%user}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-user-creator_id}}',
            '{{%user}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user-modifier_id}}',
            '{{%user}}'
        );

        // drops index for column `modifier_id`
        $this->dropIndex(
            '{{%idx-user-modifier_id}}',
            '{{%user}}'
        );
    }
}
