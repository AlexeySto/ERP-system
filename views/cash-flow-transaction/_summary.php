<?php
// Массив с данными для отображения
$summaryItems = [
    [
        'title' => 'Остаток в кассе:',
        'value' => $searchModel->getCashBalance(),
    ],
    [
        'title' => 'Итого все доходы за период:',
        'value' => $searchModel->getTotalIncome(),
    ],
    [
        'title' => 'Итого все расходы за период:',
        'value' => $searchModel->getTotalExpense(),
    ],
    [
        'title' => 'Сальдо за период:',
        'value' => $searchModel->getBalance(),
    ],
];
?>

<div class="summary-block">
    <div class="row">
        <?php foreach ($summaryItems as $item): ?>
            <div class="col-lg-3 col-md-6">
                <div class="summary-item">
                    <h4><?= $item['title'] ?></h4>
                    <p><?= Yii::$app->formatter->asDecimal($item['value'], 2) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
