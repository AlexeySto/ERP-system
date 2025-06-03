<?php

namespace app\models;

use Yii;
use himiklab\sortablegrid\SortableGridBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ObjectScoreCategory".
 *
 * @property int $id
 * @property string $title Название категории
 * @property string $description Описание
 * @property int $importance_life Важность для жизни
 * @property int $importance_investment Важность для инвестиций
 * @property int $is_deleted Запись удалена
 * @property string $created Дата создания
 * @property int|null $creator_id Создал
 * @property string|null $modified Дата изменения
 * @property int|null $modifier_id Изменил
 * @property int $section_id ID раздела
 * @property int $sort_order Порядок сортировки
 *
 * @property User $creator
 * @property User $modifier
 * @property ObjectScoreSection $section
 */
class ObjectScoreCategory extends \app\models\BaseActiveRecord
{
    public $filter_section_id;
    public $filter_title;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dict_object_score_category';
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
            [['importance_life', 'importance_investment', 'is_deleted', 'creator_id', 'modifier_id', 'section_id', 'sort_order'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 255],
            [['filter_section_id', 'filter_title'], 'safe'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectScoreSection::class, 'targetAttribute' => ['section_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Название категории'),
            'description' => Yii::t('app', 'Описание'),
            'importance_life' => Yii::t('app', 'Важность для жизни'),
            'importance_investment' => Yii::t('app', 'Важность для инвестиций'),
            'section_id' => Yii::t('app', 'Название раздела'),
            'sort_order' => Yii::t('app', 'Порядок сортировки'),
            'filter_section_id' => Yii::t('app', 'Название раздела'),
            'filter_title' => Yii::t('app', 'Название категории'),
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
     * Gets query for [[Section]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(ObjectScoreSection::class, ['id' => 'section_id']);
    }

    /**
     * Gets all unique section titles.
     *
     * @return array
     */
    public static function getSectionTitles()
    {
        return ObjectScoreSection::find()
            ->select(['title'])
            ->where(['is_deleted' => 0])
            ->indexBy('id')
            ->column();
    }

    /**
     * {@inheritdoc}
     */
    public function search($params, $recsOnPage = 20)
    {
        $dataProvider = parent::search($params, $recsOnPage);

        $query = $dataProvider->query;

        $query->andFilterWhere(['section_id' => $this->filter_section_id])
              ->andFilterWhere(['like', 'title', $this->filter_title]);

        return $dataProvider;
    }

    public static function getCacegoresName(): array
    {
        return array_unique(ArrayHelper::map(
            self::find()->where(['is_deleted' => 0])->all(), 
            'id', 'title'));
    }

    public static function getSectionsName(): array
    {
        return array_unique(ArrayHelper::map(
            self::find()->where(['is_deleted' => 0])->all(),
             'section_id', 'section.title'));
    }

    public function behaviors()
    {
        return [
            'sort' => [
                'class' => SortableGridBehavior::class,
                'sortableAttribute' => 'sort_order',
            ],
        ];
    }
}
