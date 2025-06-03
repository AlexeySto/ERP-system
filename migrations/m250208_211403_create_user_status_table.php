<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_status}}`.
 */
class m250208_211403_create_user_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->defaultValue(null),
            'comments' => $this->text()->defaultValue(null),
        ]);
		
		$this->alterColumn('{{%user_status}}', 'id', $this->tinyInteger().' NOT NULL AUTO_INCREMENT');
		
		$this->addCommentOnColumn('{{%user_status}}', 'comments', 'Любые комментарии');
		
		$this->insert('user_status', [
            'name' => 'Активный',
            'comments' => 'Пользователь может войти в свой аккаунт на сайте',
        ]);
		$this->insert('user_status', [
            'name' => 'Неактивный',
            'comments' => 'Пользователь не сможет войти в свой аккаунт на сайте',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_status}}');
    }
}
