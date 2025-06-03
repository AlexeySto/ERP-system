<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "{{%cash_flow_transaction_category}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $comments Любые комментарии
 * @property int $type_id
 * @property int|null $kind_id
 * @property int $is_deleted
 * @property string|null $created
 * @property int|null $creator_id
 * @property string|null $modified
 * @property int|null $modifier_id
 *
 * @property User $creator
 * @property CashFlowTransactionKind $kind
 * @property User $modifier
 * @property CashFlowTransactionType $type
 */
class CashFlowTransactionCategory extends BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cash_flow_transaction_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type_id'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments'], 'string'],
            [['type_id', 'kind_id', 'is_deleted', 'creator_id', 'modifier_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['kind_id'], 'exist', 'skipOnError' => true, 'targetClass' => CashFlowTransactionKind::class, 'targetAttribute' => ['kind_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modifier_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CashFlowTransactionType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' =>  Yii::t('app', 'Наименование'),
            'comments' =>  Yii::t('app', 'Комментарий'),
            'type_id' =>  Yii::t('app', 'Тип'),
            'kind_id' => Yii::t('app', 'Kind ID'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created' => Yii::t('app', 'Created'),
            'creator_id' => Yii::t('app', 'Creator ID'),
            'modified' => Yii::t('app', 'Modified'),
            'modifier_id' => Yii::t('app', 'Modifier ID'),
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id'])->inverseOf('cashFlowTransactionCategories');
    }

    /**
     * Gets query for [[Kind]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKind()
    {
        return $this->hasOne(CashFlowTransactionKind::class, ['id' => 'kind_id'])->inverseOf('cashFlowTransactionCategories');
    }

    /**
     * Gets query for [[Modifier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModifier()
    {
        return $this->hasOne(User::class, ['id' => 'modifier_id'])->inverseOf('cashFlowTransactionCategories0');
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(CashFlowTransactionType::class, ['id' => 'type_id'])->inverseOf('cashFlowTransactionCategories');
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

		if ($this->type_id) {
            $query->andWhere(['type_id' => $this->type_id]);
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

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// End:
	// Custom Methods
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
