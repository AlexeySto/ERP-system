<?php

use app\assets\EditableFieldsAsset;
use app\models\ObjectScoreCategory;
use app\grid\GridView;
use app\widgets\Card;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var common\models\ObjectScoreCategory $searchModel */

EditableFieldsAsset::register($this);

\himiklab\sortablegrid\SortableGridAsset::register($this);
$url = Url::to('sort');
$tableId = 'table-' . $this->context->id;
$this->registerJs("jQuery('#$tableId').SortableGridView('$url');");

$page = Yii::$app->request->getQueryParams()['page'] ?? 1;

$can_create = Yii::$app->user->can("backend_{$this->context->id}_create");
$can_update = Yii::$app->user->can("backend_{$this->context->id}_edit");
$can_delete = Yii::$app->user->can("backend_{$this->context->id}_delete");

$this->title = Yii::$app->urlManager->getLastTitle();
?>

<?php Card::begin([]); ?>
<?php Pjax::begin(['id' => 'pjax']) ?>

<?= $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'createRowModel' => new ObjectScoreCategory(),
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'filterSelector' => '.search-block *[name]',
        'tableOptions' => ['class' => 'table align-middle sortableAction table-check table-striped table-bordered mb-0 editable-fields', 'data-base_url' => $this->context->id, 'id' => 'table-' . $this->context->id],
        'actions' => app\widgets\ActionButtons::widget([
            'defaultShowTitle' => false,
            'defaultAccess' => '$',
            'items' => [
                ['name' => 'create', 'access' => $can_create, 'options' => ['class' => 'btn btn-success btn-sm btn-row-add', 'data-id' => 0], 'title' => 'Добавить', 'iconClass' => 'fa fa-plus'],
                $page > 0 ?
                ['name' => Url::current(['page' => -1]), 'options' => ['class' => 'btn btn-primary btn-sm'], 'title' => 'Показать все', 'iconClass' => 'fa fa-expand'] :
                ['name' => Url::current(['page' => 1]), 'options' => ['class' => 'btn btn-primary btn-sm'], 'title' => 'Показать постранично', 'iconClass' => 'fa fa-compress'],
            ]
        ]),
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '№',
            ],
            [
                'attribute' => 'section_id',
                'label' => Yii::t('app', 'Название раздела'),
                'format' => 'raw',
                'value' => function ($model) use ($can_update) {
                    $sectionTitles = ObjectScoreCategory::getSectionTitles();
                    return $can_update ? Html::dropDownList('section_id', $model->section_id, $sectionTitles, ['class' => 'form-control']) : $model->getSectionTitle();
                },
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) use ($can_update) {
                    return $can_update ? Html::input('text', 'title', $model->title, ['class' => 'form-control', 'required' => 1]) : $model->title;
                },
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($model) use ($can_update) {
                    return $can_update ? Html::input('text', 'description', $model->description, ['class' => 'form-control', 'required' => 1]) : $model->description;
                },
            ],
            [
                'attribute' => 'importance_life',
                'format' => 'raw',
                'value' => function ($model) use ($can_update) {
                    $items = array_combine(range(1, 10), range(1, 10)); // [1 => 1, 2 => 2, ..., 10 => 10]
                    return $can_update ? Html::dropDownList('importance_life', $model->importance_life, $items, ['class' => 'form-control', 'required' => 1]) : $model->importance_life;
                },
            ],
            [
                'attribute' => 'importance_investment',
                'format' => 'raw',
                'value' => function ($model) use ($can_update) {
                    $items = array_combine(range(1, 10), range(1, 10)); // [1 => 1, 2 => 2, ..., 10 => 10]
                    return $can_update ? Html::dropDownList('importance_investment', $model->importance_investment, $items, ['class' => 'form-control', 'required' => 1]) : $model->importance_investment;
                },
            ],
            [
                'label' => Yii::t('app', 'Вопросы'),
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Вопросы', ['object-score-questions', 'category_id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => Yii::t('app', 'Вы уверены, что хотите удалить эту запись?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'visible' => $can_delete,
            ]
        ],
    ]
); ?>

<?php Pjax::end() ?>
<?php Card::end(); ?>
