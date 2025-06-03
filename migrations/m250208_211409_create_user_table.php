<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m250208_211409_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = $this->db->tablePrefix . 'user';
		if ($this->db->getTableSchema($tableName, true) !== null) {
			$this->dropTable('{{%user}}');
		}
		
		$this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string(100)->defaultValue(null),
            'patronym' => $this->string(100)->defaultValue(null),
            'lastname' => $this->string(100)->defaultValue(null),
            'fullname' => $this->string(302)->defaultValue(null),
            'fio' => $this->string(255)->defaultValue(null),
            'email' => $this->string(255)->defaultValue(null),
            'phone_prefix' => $this->string(9)->defaultValue(null),
            'phone' => $this->string(31)->defaultValue(null),
            'comments' => $this->text()->defaultValue(null),
            'salary' => $this->float()->defaultValue(null),
            'job_id' => $this->integer()->defaultValue(null),
            'superior_id' => $this->integer()->defaultValue(null),
            'hire_date' => $this->date()->defaultValue(null),
            'termination_date' => $this->date()->defaultValue(null),
            'user_status_id' => $this->tinyInteger()->notNull()->defaultValue(2),
            'password_hash' => $this->string(60)->defaultValue(null),
            'auth_key' => $this->string(32)->defaultValue(null),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created' => $this->datetime()->defaultValue(null),
            'creator_id' => $this->integer()->defaultValue(null),
            'modified' => $this->datetime()->defaultValue(null),
            'modifier_id' => $this->integer()->defaultValue(null),
        ]);

		$this->addCommentOnTable('{{%user}}', 'Человек. Может быть пользователем или не быть. Может быть сотрудником или не быть.');
		$this->addCommentOnColumn('{{%user}}', 'fullname', 'Строка из полных фамилии, имени, отчества');
		$this->addCommentOnColumn('{{%user}}', 'fio', 'Строка из фамилии и инициалов');
		$this->addCommentOnColumn('{{%user}}', 'phone_prefix', 'Префикс телефона (код страны + код города (если есть)');
		$this->addCommentOnColumn('{{%user}}', 'phone', 'Префикс телефона (Номер телефона без кода страны и кода города');
		$this->addCommentOnColumn('{{%user}}', 'comments', 'Любые комментарии');
		$this->addCommentOnColumn('{{%user}}', 'salary', 'Оклад');
		$this->addCommentOnColumn('{{%user}}', 'job_id', 'Должность');
		$this->addCommentOnColumn('{{%user}}', 'superior_id', 'Непосредственный руководитель');
		$this->addCommentOnColumn('{{%user}}', 'hire_date', 'Дата найма в качестве сотрудника (или внешнего контрагента)');
		$this->addCommentOnColumn('{{%user}}', 'termination_date', 'Дата прекращения отношений с сотрудником (или внешним контрагентом)');
		$this->addCommentOnColumn('{{%user}}', 'user_status_id', 'Статус именно как пользователя (не человека или сотрудника)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
