<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%cash_flow_transaction_kind}}".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $comments Любые комментарии
 * @property int $is_deleted
 *
 * @property CashFlowTransactionCategory[] $cashFlowTransactionCategories
 */
class CashFlowTransactionKind extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cash_flow_transaction_kind}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required', 'on' => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE]],
            [['comments'], 'string'],
            [['is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['short_name'], 'string', 'max' => 16],
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
            'short_name' => Yii::t('app', 'Short Name'),
            'comments' => Yii::t('app', 'Comments'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    /**
     * Gets query for [[CashFlowTransactionCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashFlowTransactionCategories()
    {
        return $this->hasMany(CashFlowTransactionCategory::class, ['kind_id' => 'id'])->inverseOf('kind');
    }
}
