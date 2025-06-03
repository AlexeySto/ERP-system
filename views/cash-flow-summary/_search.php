<?php

use app\models\CashFlowSummary;
use app\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\CashFlowSummary */
/* @var $form yii\widgets\ActiveForm */

// Получаем массив годов
$years = CashFlowSummary::getTransactionYears();

// Преобразуем массив годов, чтобы ключами и значениями были годы
$years = array_combine($years, $years);

// Определяем текущий выбранный год (по умолчанию — текущий)
$currentYear = $model->year ?? date('Y');

?>

<div class="form-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => true],
        'id' => 'search-form',
    ]);
    ?>

    <div>
        <?= $form->field($model, 'year')->widget(Select2::class, [
            'data' => $years,
            'options' => [
                'placeholder' => 'Выберите год',
                'value' => $currentYear, // Устанавливаем текущий выбранный год
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label('Год'); ?>
    </div>

    <?php ActiveForm::end(); ?>
    <hr/>
</div>

<?php
$this->registerJs(<<<JS
    $(document).ready(function() {
        // Восстановление выбранного года из sessionStorage
        var savedYear = sessionStorage.getItem('selectedYear');
        if (savedYear) {
            $('select[name="CashFlowSummary[year]"]').val(savedYear).trigger('change');
        }

        // При изменении выбора сохраняем в sessionStorage и отправляем форму
        $('#search-form').on('change', 'select[name="CashFlowSummary[year]"]', function() {
            var selectedValue = $(this).val();
            sessionStorage.setItem('selectedYear', selectedValue);
            $('#search-form').submit();
        });
    });
JS
);
?>
