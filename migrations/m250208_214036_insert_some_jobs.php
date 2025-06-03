<?php

use yii\db\Migration;
use app\models\JobType;

/**
 * Class m250208_214036_insert_some_jobs
 */
class m250208_214036_insert_some_jobs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->batchInsert(
			'job',
            ['name', 'comments', 'type_id', 'division_id', 'created', 'creator_id'],
            [
				['Владелец', 'Владелец или совладелец бизнеса', JobType::ENGINEER_OR_TECHNICIAN, 1, date('Y-m-d H:i:s'), 1],
				['Директор по производству', null, JobType::ENGINEER_OR_TECHNICIAN, 5, date('Y-m-d H:i:s'), 1],
				['Начальник производства', null, JobType::ENGINEER_OR_TECHNICIAN, 5, date('Y-m-d H:i:s'), 1],
				['Бухгалтер', null, JobType::ENGINEER_OR_TECHNICIAN, 4, date('Y-m-d H:i:s'), 1],
				['Менеджер по работе с клиентами', null, JobType::ENGINEER_OR_TECHNICIAN, 3, date('Y-m-d H:i:s'), 1],
				['Логист', null, JobType::ENGINEER_OR_TECHNICIAN, 7, date('Y-m-d H:i:s'), 1],
				['Кладовщик', null, JobType::ENGINEER_OR_TECHNICIAN, 6, date('Y-m-d H:i:s'), 1],
				['Рабочий', null, JobType::WORKER, 5, date('Y-m-d H:i:s'), 1],
				['Маркетолог', null, JobType::ENGINEER_OR_TECHNICIAN, 2, date('Y-m-d H:i:s'), 1],
			]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250208_214036_insert_some_jobs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250208_214036_insert_some_jobs cannot be reverted.\n";

        return false;
    }
    */
}
