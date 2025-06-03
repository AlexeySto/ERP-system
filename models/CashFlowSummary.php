<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class CashFlowSummary extends CashFlowTransaction
{
    public $year;

    public function search($params, $recsOnPage = 20)
    {
        // Загружаем параметры
        $this->load($params);

        // Если год не указан, используем текущий
        if (!$this->year) {
            $this->year = date('Y');
        }

        // Формируем диапазон дат для выбранного года
        $startDate = $this->year . '-01-01';
        $endDate = $this->year . '-12-31';

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

        // Добавляем фильтр по диапазону дат
        $query->andWhere(['between', 'cash_flow_transaction.created', trim($startDate), trim($endDate)]);

        // Создаем DataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $recsOnPage,
            ],
        ]);

        // Вычисляем сводку данных
        $summary = $this->calculateSummary($dataProvider->getModels());
        $gridData = $this->prepareGridData($summary);

        // Создаем DataProvider
        $arrayDataProvider = new ArrayDataProvider([
            'allModels' => $gridData,
            'pagination' => false,
        ]);

        return $arrayDataProvider;
    }

    private function calculateSummary($models)
    {
        $summary = [
            'income' => [],
            'expense' => [],
            'monthlyTotals' => array_fill(1, 12, 0), // Разница по месяцам
        ];

        foreach ($models as $model) {
            $type = $model->getType() === 'Приход' ? 'income' : 'expense';
            $categoryName = $model->cashFlowTransactionCategory->name;
            $month = (int)date('m', strtotime($model->created));
            $amount = (float)$model->amount;

            if (!isset($summary[$type][$categoryName])) {
                $summary[$type][$categoryName] = array_fill(1, 12, 0);
            }

            $summary[$type][$categoryName][$month] += $amount;

            // Обновляем total
            if ($type === 'income') {
                if (!isset($summary[$type]['total'])) {
                    $summary[$type]['total'] = array_fill(1, 12, 0);
                }
                $summary[$type]['total'][$month] += $amount;
            } elseif ($type === 'expense') {
                if (!isset($summary[$type]['total'])) {
                    $summary[$type]['total'] = array_fill(1, 12, 0);
                }
                $summary[$type]['total'][$month] += $amount;
            }
        }

        // Вычисляем разницу между Приходом и Расходом для monthlyTotals
        for ($month = 1; $month <= 12; $month++) {
            $income = isset($summary['income']['total'][$month]) ? $summary['income']['total'][$month] : 0;
            $expense = isset($summary['expense']['total'][$month]) ? $summary['expense']['total'][$month] : 0;
            $summary['monthlyTotals'][$month] = $income - $expense;
        }

        return $summary;
    }

    public function prepareGridData($summary)
    {
        $gridData = [];

        if ($summary) {
            $types = ['income', 'expense'];
            foreach ($types as $type) {
                if (isset($summary[$type]['total'])) {
                    // Добавляем разрыв в таблице перед заголовком типа
                    $gridData[] = ['isDivider' => true];

                    // Заголовок для типа
                    $gridData[] = [
                        'isHeader' => true,
                        'label' => $type === 'income' ? 'Приход' : 'Расход',
                        'total' => array_sum($summary[$type]['total']),
                        'months' => $summary[$type]['total'],
                    ];

                    // Сбрасываем нумерацию строк для категорий
                    $rowNumber = 1;

                    // Категории
                    foreach ($summary[$type] as $category => $monthlySums) {
                        if ($category !== 'total') {
                            $gridData[] = [
                                'isCategory' => true,
                                'rowNumber' => $rowNumber++,
                                'category' => $category,
                                'total' => array_sum($monthlySums),
                                'months' => $monthlySums,
                            ];
                        }
                    }
                }
            }

            // Итоговое сальдо
            if (isset($summary['monthlyTotals'])) {
                $gridData[] = ['isDivider' => true];
                $gridData[] = [
                    'isHeader' => true,
                    'label' => 'Остаток (итоговое сальдо)',
                    'total' => array_sum($summary['monthlyTotals']),
                    'months' => $summary['monthlyTotals'],
                ];
            }
        }

        return $gridData;
    }

    /**
 * Возвращает массив уникальных годов, в которых есть транзакции.
 * Формат: [2025, 2024, 2023, ...]
 *
 * @return array
 */
    public static function getTransactionYears()
    {
        // Получаем минимальную и максимальную дату транзакций
        $minYear = self::find()
            ->min('YEAR(created)');

        $maxYear = self::find()
            ->max('YEAR(created)');

        if (!$minYear || !$maxYear) {
            // Если транзакций нет, возвращаем текущий год
            $currentYear = date('Y');
            return [$currentYear];
        }

        // Создаем массив годов от max до min
        $years = range($maxYear, $minYear);

        return $years;
    }
}
