<?php

use app\assets\EditableFieldsAsset;
use app\grid\GridViewClean;
use app\models\CashFlowTransaction;
use app\widgets\Card;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashFlowTransaction */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_cash-flow-transaction_edit');
$can_create = Yii::$app->user->can('app_cash-flow-transaction_create');
$can_delete = Yii::$app->user->can('app_cash-flow-transaction_delete');
?>

<?php Card::begin(); ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<hr/>

<?php Pjax::begin(['id' => 'pjax']) ?>

<?= $this->render('_summary', ['searchModel' => $searchModel]); ?>

<hr/>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new CashFlowTransaction(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'tableOptions' => [
        'class' => 'table align-middle table-check table-bordered mb-0 editable-fields',
        'data-base_url' => 'cash-flow-transaction',
        'id' => 'table-cash-flow-transaction'
    ],
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '№ п/п'
        ],
        [
            'attribute' => 'created',
            'format' => 'raw',
            'value' => function ($model) use ($can_update) {
                if ($can_update) {
                    $date = $model->created ? date('Y-m-d', strtotime($model->created)) : date('Y-m-d');
                    return Html::input('date', 'created', $date, [
                        'class' => 'form-control',
                    ]);
                } else {
                    return Yii::$app->formatter->asDate($model->created, 'php:d.m.Y');
                }
            },
        ],
        [
            'attribute' => 'cash_flow_transaction_category_id',
            'format' => 'raw',
            'value' => function ($model) use ($can_update) {
                return $can_update ? Html::dropDownList(
                    'cash_flow_transaction_category_id',
                    $model->cash_flow_transaction_category_id,
                    CashFlowTransaction::getCashFlowTransactionCategoryList(),
                    [
                        'class' => 'form-control form-select select2',
                        'style' => 'width: 100%',
                    ]) : $model->cashFlowTransactionCategory->name ?? '';
            },
        ],
        [
            'attribute' => 'type',
            'format' => 'raw',
            'value' => function($model)  {
                return $model->getType();
            },
        ],
        [
            'attribute' => 'account_type_id',
            'format' => 'raw',
            'value' => function ($model) use ($can_update) {
                return $can_update ? Html::dropDownList(
                    'account_type_id',
                    $model->account_type_id,
                    CashFlowTransaction::getAccountTypes(),
                    [
                        'class' => 'form-control form-select',
                        'style' => 'width: 100%',
                    ]) : CashFlowTransaction::getAccountTypes()[$model->account_type_id];
            },
        ],
        [
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'comments', $model->comments ?? '', [
                    'class' => 'form-control',
                ]) : $model->comments ?? '';
            },
        ],
        [
            'attribute' => 'amount',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('number', 'amount', $model->amount, [
                    'class' => 'form-control',
                ]) : $model->amount;
            },
        ],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'visible' => $can_delete,
            'buttons' => [
                'delete' => [
                    'icon' => 'fa fa-trash',
                    'class' => 'btn btn-danger btn-sm',
                    'title' => 'Удалить',
                    'confirm' => 'Удалить?',
                    'isPost' => true,
                ],
            ],
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

<hr>

<?php
Card::end();
?>
