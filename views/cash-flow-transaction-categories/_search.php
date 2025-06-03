<?php

use kartik\select2\Select2;
use app\widgets\ActiveForm;
use app\models\CashFlowTransactionType;

/* @var $this yii\web\View */
/* @var $model app\models\CashFlowTransactionType */
/* @var $form yii\widgets\ActiveForm */

$fieldsConfig = [
    [
        'attribute' => 'type_id',
        'widget' => Select2::class,
        'data' => CashFlowTransactionType::listAll('id', 'name'),
        'placeholder' => 'Все',
        'label' => 'Тип',
    ],
    [
        'attribute' => 'name',
        'type' => 'textInput',
        'maxlength' => true,
        'id' => 'period',
        'autocomplete' => 'off',
        'label' => 'Название',
    ],
    [
        'attribute' => 'comments',
        'type' => 'textInput',
        'maxlength' => true,
        'id' => 'period',
        'autocomplete' => 'off',
        'label' => 'Комментарии',
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
