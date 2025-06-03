<?php

namespace app\models;

use app\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "cash_flow_transaction".
 *
 * @property int $id
 * @property string|null $created
 * @property int $is_deleted
 * @property string|null $comments
 * @property int $status
 * @property int $cash_flow_transaction_category_id
 * @property int $account_type_id
 * @property int|null $creator_id
 * @property int|null $amount
 * @property string|null $modified
 * @property int|null $modifier_id
 * @property string|null $type
 * @property string|null $total_income
 * @property string|null $total_expense
 * @property string|null $balance
 * @property string|null $cash_balance
 */
class CashFlowTransaction extends BaseActiveRecord
{
    const ACCOUNT_TYPE_BOX = 1;
    const ACCOUNT_TYPE_OTHER = 2;

    public $type; 
    private $total_income;
    private $total_expense;
    private $balance;
    private $cash_balance;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cash_flow_transaction';
    }

    /**
     * Gets query for [[CashFlowTransactionCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCashFlowTransactionCategory()
    {
        return $this->hasOne(CashFlowTransactionCategory::class, ['id' => 'cash_flow_transaction_category_id']);
    }

    /**
     * Gets query for [[CashFlowTransactionType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeRelation()
    {
        return $this->hasOne(CashFlowTransactionType::class, ['id' => 'type_id'])->via('cashFlowTransactionCategory');
    }

    /**
     * Геттер для атрибута type
     */
    public function getType()
    {
        $category = CashFlowTransactionCategory::findOne($this->cash_flow_transaction_category_id);
        
        if ($category && $category->type_id) {
            $type = CashFlowTransactionType::findOne($category->type_id);
            return strtolower($type->name);
        }
        return null;
    }

    /**
     * Геттер для атрибута total_income
     */
    public function getTotalIncome()
    {
        return $this->total_income;
    }

    /**
     * Геттер для атрибута total_expense
     */
    public function getTotalExpense()
    {
        return $this->total_expense;
    }
        
    /**
     * Геттер для атрибута balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Геттер для атрибута cash_balance
     */
    public function getCashBalance()
    {
        return $this->cash_balance;
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->type = $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created', 'comments', 'creator_id', 'modified', 'modifier_id'], 'default', 'value' => null],
            [['status', 'amount'], 'default', 'value' => 0],
            [['created', 'modified'], 'safe'],
            [['is_deleted', 'status', 'cash_flow_transaction_category_id', 'creator_id', 'modifier_id', 'account_type_id'], 'integer'],
            [['comments'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Дата',
            'is_deleted' => 'Is Deleted',
            'comments' => 'Наименование',
            'status' => 'Статус',
            'cash_flow_transaction_category_id' => 'Категория ДДС',
            'account_type_id' => 'Тип счета',
            'creator_id' => 'Creator ID',
            'modified' => 'Modified',
            'modifier_id' => 'Modifier ID',
            'amount' => 'Количество',
            'type' => 'Тип',
        ];
    }

    public function search($params, $recsOnPage = 20)
    {
        if (key_exists('per-page', $params)) {
            $recsOnPage = (int)$params['per-page'];
        }
        
        $query = self::find()
            ->joinWith([
                'cashFlowTransactionCategory' => function ($q) {
                    $q->andWhere(['cash_flow_transaction_category.is_deleted' => 0]);
                },
                'cashFlowTransactionCategory.type' => function ($q) {
                    $q->andWhere(['cash_flow_transaction_type.is_deleted' => 0]);
                },
            ])
            ->andWhere(['cash_flow_transaction.is_deleted' => 0]);
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $recsOnPage,
            ],
        ]);
    
        if (!$this->load($params) || !$this->validate()) {
            $this->calculateSums();
            return $dataProvider;
        }
        // Получение дополнительных данных
        $startDate = null;
        $endDate = null;
        if ($this->created !== null) {
            $date = explode(" - ", $this->created);
            $startDate = $date[0] ?? null;
            $endDate = $date[1] ?? null;
        }
        $this->type = $params['CashFlowTransaction']['type'];
    
        // Обработка фильтрации по датам
        if ($this->created) {
            if (count($date) === 2) {
                $query->andWhere(['between', 'cash_flow_transaction.created', trim($startDate), trim($endDate)]);
            }
        }
    
        $query->andFilterWhere(['like', 'cash_flow_transaction.comments', $this->comments])
              ->andFilterWhere(['cash_flow_transaction.cash_flow_transaction_category_id' => $this->cash_flow_transaction_category_id])
              ->andFilterWhere(['cash_flow_transaction.account_type_id' => $this->account_type_id]);
    
        // фильтр по имени типа
        if ($this->type !== null && $this->type !== '') {
            $query->andFilterWhere(['like', 'cash_flow_transaction_type.name', $this->type]);
        }
    
        // Расчет сумм
        $this->calculateSums($startDate, $endDate);
    
        return $dataProvider;
    }

    public static function getAccountTypes(): array
    {
        return [
            self::ACCOUNT_TYPE_BOX => "Касса",
            self::ACCOUNT_TYPE_OTHER => "Другое",
        ];
    }

    public static function getCashFlowTransactionCategoryList(): array
    {
        return array_unique(ArrayHelper::map(
            CashFlowTransactionCategory::find()->where(['is_deleted' => 0])->all(),
            'id', 'name'
        ));
    }

    public static function getTransactionTypes(): array
    {
        return array_unique(ArrayHelper::map(
            CashFlowTransactionType::find()->where(['is_deleted' => 0])->all(),
            'name', 'name'
        ));
    }

        /**
     * Получить сумму всех доходов за заданный период
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    private static function setTotalIncome($startDate = null, $endDate = null)
    {
        return self::getTotalByType('Приход', $startDate, $endDate);
    }

    /**
     * Получить сумму всех расходов за заданный период
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    private static function setTotalExpense($startDate = null, $endDate = null)
    {
        return self::getTotalByType('Расход', $startDate, $endDate);
    }

    /**
     * Получить сальдо за период
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    private static function setBalance($startDate = null, $endDate = null)
    {
        return self::setTotalIncome($startDate, $endDate) - self::setTotalExpense($startDate, $endDate);
    }

    /**
     * Получить сумму в кассе
     * @return float
     */
    private static function setCashBalance()
    {
        return self::setTotalIncome() - self::setTotalExpense();
    }

    /**
     * Получить сумму транзакций за заданный период по типу
     * @param string $typeName Название типа транзакции ('Приход' или 'Расход')
     * @param string|null $startDate Начальная дата периода
     * @param string|null $endDate Конечная дата периода
     * @return float
     */
    private static function getTotalByType($typeName, $startDate = null, $endDate = null)
    {
        $query = self::find()
            ->joinWith(['cashFlowTransactionCategory' => function ($q) {
                $q->andWhere(['cash_flow_transaction_category.is_deleted' => 0]);
            }])
            ->joinWith(['cashFlowTransactionCategory.type' => function ($q) {
                $q->andWhere(['cash_flow_transaction_type.is_deleted' => 0]);
            }])
            ->andWhere(['cash_flow_transaction_type.name' => $typeName])
            ->andWhere(['cash_flow_transaction.is_deleted' => 0]);

        if ($startDate && $endDate) {
            $query->andWhere(['between', 'cash_flow_transaction.created', $startDate, $endDate]);
        }

        return (float)$query->sum('cash_flow_transaction.amount') ?: 0;
    }

    private function calculateSums($startDate = null, $endDate = null)
    {
        $this->total_income = self::setTotalIncome($startDate, $endDate);
        $this->total_expense = self::setTotalExpense($startDate, $endDate);
        $this->balance = self::setBalance($startDate, $endDate);
        $this->cash_balance = self::setCashBalance();
    }
}
