<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ObjectScoreSection".
 *
 * @property int $id
 * @property string $title Название раздела
 * @property string $description Описание
 * @property int $importance_life Важность для жизни
 * @property int $importance_investment Важность для инвестиций
 * @property int $is_deleted Запись удалена
 * @property string $created Дата создания
 * @property int|null $creator_id Создал
 * @property string|null $modified Дата изменения
 * @property int|null $modifier_id Изменил
 *
 * @property User $creator
 * @property User $modifier
 */
class ObjectScoreSection extends \app\models\BaseActiveRecord
{
    public $search;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dict_object_score_section';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['importance_investment'], 'default', 'value' => 5],
            [['importance_life'], 'default', 'value' => 5],
            [['is_deleted'], 'default', 'value' => 0],
            [['title'], 'required'],
            [['importance_life', 'importance_investment', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 255],
            [['search'], 'string'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Название раздела'),
            'description' => Yii::t('app', 'Описание'),
            'importance_life' => Yii::t('app', 'Важность для жизни'),
            'importance_investment' => Yii::t('app', 'Важность для инвестиций'),
            'category_link' => Yii::t('app', 'Категории'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
            'search' => Yii::t('app', 'Поиск'),
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id']);
    }
    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;

        if ($this->search) {
            $query->andWhere(['or', 
            ['like', 'title', $this->search],
            ['like', 'description', $this->search],
            ]);
        }

        // Return data provider
        return $dataProvider;
    }
}
