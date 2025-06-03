<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m250206_135734_add_admin_user
 */
class m250206_135734_add_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		if (!(new Query())->select('*')->from('user')->where(['id' => 1])->exists()) {
			$this->insert('user', [
				'id' => '1',
				'username' => 'Admin',
				'auth_key' => 'bcwFsemErvaYiaRhpq7XA2cCktGVMYor',
				'password_hash' => '$2y$13$YlKZqezLvhF.TH0dZW0ALevDZqDhYsh2445ggQZuGFkQ3W3zH63v6',
				'email' => 'admin@pkmm.ru',
				'status' => '10',
				'created_at' => '1738701198',
				'updated_at' => '1738701752',
				'verification_token' => 'gp9i_EgiwytXNCOu2DxUX86ssFeHs0CU_1738701198',
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
