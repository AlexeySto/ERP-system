<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%job_type}}`.
 */
class m250208_211402_create_job_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%job_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'comments' => $this->text()->defaultValue(null),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
		
		$this->alterColumn('{{%job_type}}', 'id', $this->smallInteger().' NOT NULL AUTO_INCREMENT');
		
		$this->addCommentOnColumn('{{%job_type}}', 'comments', 'Любые комментарии');
		
		$this->insert('job_type', [
            'name' => 'Рабочий',
            'comments' => 'Рабочие должности',
        ]);
		$this->insert('job_type', [
            'name' => 'ИТР',
            'comments' => 'Инженерно-технические работники',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%job_type}}');
    }
}
