<?php

return [
    ['icon' => 'fas fa-industry', 'label' => 'Производство', 'items' => [
    ]],
    ['icon' => 'fab fa-whmcs', 'label' => 'Справочники', 'items' => [
        ['icon' => 'fas fa-users', 'label' => 'Категории ДДС', 'url' => '/cash-flow-transaction-categories', 'group' => 6],
        ['label' => 'Категории оценки объекта', 'icon' => 'fas fa-chart-line', 'url' => '/object-score-category', 'group' => 6],
    ]],
    ['icon' => 'bi bi-currency-exchange', 'label' => 'ЗП, смены и кассы', 'items' => [
        ['icon' => 'bi bi-check-circle', 'label' => 'Движение денежных средств', 'url' => '/cash-flow-transaction', 'group' => 1],
        ['icon' => 'bi bi-check-circle', 'label' => 'Свод ДДС', 'url' => '/cash-flow-summary', 'group' => 1],
    ]],
];