<?php

use yii\helpers\Html;
?>

<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="form-group">
        <label for="" class="control-label">&nbsp;</label>
        <div class="input-group0">
            <?= Html::submitButton('<i class="fa fa-trash"></i>', [
                'class' => 'btn btn-default btn-outline-dark',
                'title' => 'Очистить',
                'onclick' => "var form = jQuery(this.form); form.find('input[type=text]').val(''); form.find('select').val(''); return true;"
            ]) ?>
        </div>
    </div>
</div>