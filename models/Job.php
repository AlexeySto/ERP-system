<?php

namespace app\models;

use app\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "{{%job}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $comments Любые комментарии
 * @property int|null $type_id
 * @property int|null $division_id
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property Division $division
 * @property User $modifier
 * @property JobType $type
 * @property User[] $users
 */
class Job extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%job}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
			[['comments'], 'string'],
            [['type_id', 'division_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::class, 'targetAttribute' => ['division_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => JobType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Наименование'),
            'comments' => Yii::t('app', 'Комментарии'),
            'type_id' => Yii::t('app', 'Type ID'),
            'division_id' => Yii::t('app', 'Подразделение'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),

            'type' => Yii::t('app', 'Тип'),
            'division' => Yii::t('app', 'Подразделение'),
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('jobs');
    }

    /**
     * Gets query for [[Division]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::class, ['id' => 'division_id'])->inverseOf('jobs');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('jobs0');
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(JobType::class, ['id' => 'type_id'])->inverseOf('jobs');
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['job_id' => 'id'])->inverseOf('job');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        if(key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;

		if ($this->division_id) {
            $query->andWhere(['division_id' => $this->division_id]);
        }
		if ($this->name) {
            $query->andWhere(['like', 'name', $this->name]);
        }
		if ($this->comments) {
            $query->andWhere(['like', 'comments', $this->comments]);
        }

		// Return data provider
        return $dataProvider;
    }

    public static function getList(): array
    {
        return ArrayHelper::map(static::find()->where(['is_deleted' => 0])->all(), 'id', 'name');
    }

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
