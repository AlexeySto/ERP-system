<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_status}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $comments Любые комментарии
 *
 * @property User[] $users
 */
class UserStatus extends \yii\db\ActiveRecord
{
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_status}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comments'], 'string'],
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
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['user_status_id' => 'id'])->inverseOf('userStatus');
    }
}
