<?php

use app\models\CashFlowTransaction;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use app\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashFlowTransaction */

$fieldsConfig = [
    [
        'attribute' => 'cash_flow_transaction_category_id',
        'widget' => Select2::class,
        'data' => CashFlowTransaction::getCashFlowTransactionCategoryList(),
        'placeholder' => 'Все',
        'label' => 'Категория ДДС',
    ],
    [
        'attribute' => 'type',
        'widget' => Select2::class,
        'data' => CashFlowTransaction::getTransactionTypes(),
        'placeholder' => 'Все',
        'label' => 'Тип',
    ],
    [
        'attribute' => 'created',
        'widget' => DateRangePicker::class,
        'pluginOptions' => [
            'allowClear' => true,
            'locale' => ['format' => 'YYYY-MM-DD'],
        ],
        'label' => 'Дата',
    ],
    [
        'attribute' => 'comments',
        'type' => 'textInput',
        'maxlength' => true,
        'id' => 'period',
        'autocomplete' => 'off',
        'label' => 'Комментарии',
    ],
    [
        'attribute' => 'account_type_id',
        'widget' => Select2::class,
        'data' => CashFlowTransaction::getAccountTypes(),
        'placeholder' => 'Все',
        'label' => 'Тип счета',
    ],
];

?>

<div class="form-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => true],
        'id' => 'search-form',
    ]); ?>

    <div class="row search-block">
        <?php foreach ($fieldsConfig as $field): ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <?= $this->render('../search/_filter', ['form' => $form, 'model' => $model, 'field' => $field]); ?>
            </div>
        <?php endforeach; ?>
        
        <?= $this->render('../search/_button-clear'); ?>

    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<JS
$(document).ready(function() {
    // Сохраняем выбранное значение в sessionStorage
    $('#search-form').on('change', 'select[name^="CashFlowTransactionSearch["]', function() {
        var selectedValue = $(this).val();
        var name = $(this).attr('name');
        sessionStorage.setItem(name, selectedValue);
    });

    // Восстановление значений из sessionStorage
    $('select[name^="CashFlowTransactionSearch["]').each(function() {
        var name = $(this).attr('name');
        var savedVal = sessionStorage.getItem(name);
        if (savedVal !== null) {
            $(this).val(savedVal).trigger('change');
        }
    });

    // Обновление формы при любом изменении
    $('#search-form').on('change', 'input, select', function() {
        $('#search-form').submit();
    });
});
JS
);
?>
