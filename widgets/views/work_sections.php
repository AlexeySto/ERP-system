<?php

use app\models\OrderWorkSections;
use yii\helpers\Html;

/** @var \app\models\Order $model */

?>
<table class="table align-middle table-check" style="margin: 0;">
    <thead>
    <tr style="">
        <?php foreach ($model->workSections as $section):
            /** @var OrderWorkSections $section */ ?>
            <td><b><?= $section->workSection->name ?></b></td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <tr style="">
        <?php
        foreach ($model->workSections as $section):
            /** @var OrderWorkSections $section */ ?>
            <td><?= Html::dropDownList('order-work-section-status', $section->status, OrderWorkSections::STATUSES, ['class' => 'form-control', 'data-order-id' => $model->id, 'data-section-id' => $section->id]) ?></td>
        <?php endforeach; ?>
    </tr>
    </tbody>
</table>