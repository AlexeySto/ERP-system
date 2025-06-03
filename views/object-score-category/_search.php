<?php

use kartik\select2\Select2;
use app\widgets\ActiveForm;
use app\models\ObjectScoreCategory;


/* @var $this yii\web\View */
/* @var $model common\models\ObjectScoreCategory */
/* @var $form yii\widgets\ActiveForm */

$fieldsConfig = [
    [
        'attribute' => 'filter_section_id',
        'widget' => Select2::class,
        'data' => ObjectScoreCategory::getSectionsName(),
        'placeholder' => 'Все',
        'label' => 'Название раздела',
    ],
    [
        'attribute' => 'filter_title',
        'widget' => Select2::class,
        'data' => ObjectScoreCategory::getCacegoresName(),
        'placeholder' => 'Все',
        'label' => 'Название категории',
    ],
];

?>

<div class="form-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row search-block">
        <?php foreach ($fieldsConfig as $field): ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?= $this->render('../search/_filter', ['form' => $form, 'model' => $model, 'field' => $field]); ?>
            </div>
        <?php endforeach; ?>
        
        <?= $this->render('../search/_button-clear', ['form' => $form]); ?>
    </div>

    <?php ActiveForm::end(); ?>
    <hr/>
</div>
