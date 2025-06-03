<?php

use app\grid\GridViewClean;
use app\models\CashFlowTransactionCategory;
use app\widgets\Card;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashFlowTransactionCategory */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();

\app\assets\EditableFieldsAsset::register($this);

$can_update = Yii::$app->user->can('app_cash-flow-transaction-categories_edit');
$can_create = Yii::$app->user->can('app_cash-flow-transaction-categories_create');
$can_delete = Yii::$app->user->can('app_cash-flow-transaction-categories_delete');

?>

<?php Card::begin([]); ?>

<?= $this->render('_search', ['model' => $searchModel, 'data' => ['cft_types' => $data['cft_types']]]); ?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax']) ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'showPageSize' => true,
    'createRowModel' => new CashFlowTransactionCategory(),
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'filterSelector' => 'select[name="per-page"], .search-block *[name]',
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered mb-0 editable-fields', 'data-base_url' => 'cash-flow-transaction-categories', 'id' => 'table-cash-flow-transaction-categories'],
    'actions' => $can_create ? '<span class="btn btn-success btn-sm btn-row-add"><span class="fa fa-plus"></span></span>' : '',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'name', $model->name, ['class' => 'form-control', 'required' => 1]) : $model->name;
            },
        ],
        [
            'attribute' => 'type_id',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? \yii\helpers\Html::dropDownList('type_id', $model->type_id,
                    \app\models\CashFlowTransactionType::listAll('id', 'name'),
                    ['class' => 'form-control form-select', 'style' => 'width: 100%']) : $data['cft_types'][$model->type_id] ?? '';
            },
        ],
		[
            'attribute' => 'comments',
            'format' => 'raw',
            'value' => function($model) use ($can_update) {
                return $can_update ? Html::input('text', 'comments', $model->comments, ['class' => 'form-control']) : $model->comments;
            },
        ],
        [
            'class' => 'app\grid\ActionColumn',
            'defaultShowTitle' => false,
            'visible' => $can_delete,
            'buttons' => [
                'delete' => ['icon' => 'fa fa-trash', 'class' => 'btn btn-danger btn-sm btn-row-remove', 'title' => 'Удаление'],
            ],
        ],
    ],
]);
?>

<?php \yii\widgets\Pjax::end() ?>

<hr>

<?php
Card::end();
?>

