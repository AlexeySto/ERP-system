<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m250208_214030_add_admin_user
 */
class m250208_214030_add_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		if (!(new Query())->select('*')->from('user')->where(['id' => 1])->exists()) {
			$this->insert('user', [
				'id' => 1,
				'firstname' => 'Даниил',
				'patronym' => 'Васильевич',
				'lastname' => 'Ермилов',
				'fullname' => 'Ермилов Даниил Васильевич',
				'fio' => 'Ермилов Д.В.',
				'email' => 'admin@pkmm.ru',
				'user_status_id' => 1,
				'creator_id' => 1,
				'created' => '2025-02-09 12:00:00',
				'auth_key' => 'bcwFsemErvaYiaRhpq7XA2cCktGVMYor',
				'password_hash' => '$2y$13$YlKZqezLvhF.TH0dZW0ALevDZqDhYsh2445ggQZuGFkQ3W3zH63v6',
			]);
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('user', ['id' => 1]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250206_135734_add_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
