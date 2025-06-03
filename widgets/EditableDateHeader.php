<?php

namespace app\widgets;

use yii\web\View;
use yii\base\Widget;
use kartik\date\DatePicker;

class EditableDateHeader extends Widget
{
    public $date;
    public $year;
    public $attribute;
    public string $format = 'mm.dd';
    public string $action;
    public string $container;
    public int $typeId = 0;

    public function run()
    {
        $this->registerClientScript();
        $this->registerCSS();
        $defaultHeader = "<span class='editable-date link-primary' data-date='{$this->date}'>{$this->date}</span>";
        $datePicker = "<span class='date-picker-container' style='display:none;'>
            " . DatePicker::widget([
               'name'          => "date_picker_{$this->date}",
               'value'         => $this->date,
               'options'       => [
                   'class'     => 'date-header-picker',
                   'data-date' => $this->date,
               ],
               'pluginOptions' => [
                   'format'         => $this->format,
                   'autoclose'      => true,
               ],
            ]) . "
        </span>";
        
        return $defaultHeader.$datePicker;
    }

    protected function registerClientScript()
    {
        $js = <<<JS
        $(document).on('click', '.editable-date', function() {
            var container = $(this).closest('th');
            container.find('.editable-date').hide();
            container.find('.date-picker-container').show();
            container.find('.date-header-picker').focus();
        });

        $(document).on('change', '.date-header-picker', function() {
            let newDate = $(this).val();
            let oldDate = $(this).data('date');

            $.ajax({
                url: '{$this->action}',
                type: 'PATCH',
                data: { old_date: oldDate, new_date: newDate, year: {$this->year}, type_id: {$this->typeId}},
                success: function () {
                    $.pjax.reload({container: "#{$this->container}", async: false});
                },
                error: function () {
                    alert('Ошибка обновления даты');
                }
            });
        });

        $(document).on('blur', '.date-header-picker', function() {
            var container = $(this).closest('th');
            container.find('.date-picker-container').hide();
            container.find('.editable-date').text($(this).val()).show();
        });
        JS;

        $this->view->registerJs($js, View::POS_READY);
    }

    public function registerCSS()
    {
        $css = <<<CSS
        .editable-date:hover{
            cursor: pointer;
        }
        CSS;

        $this->view->registerCss($css);
    }
}