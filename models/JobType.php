<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%job_type}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 *
 * @property Job[] $jobs
 */
class JobType extends \yii\db\ActiveRecord
{
	const WORKER = 1;
	const ENGINEER_OR_TECHNICIAN = 2;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%job_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
			[['comments'], 'string'],
            [['is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'comments' => Yii::t('app', 'Comments'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    /**
     * Gets query for [[Jobs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::class, ['type_id' => 'id'])->inverseOf('type');
    }
}
