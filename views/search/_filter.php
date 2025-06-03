<?php
    if (isset($field['widget'])) {
        // Вариант с виджетом
        echo $form->field($model, $field['attribute'])->widget($field['widget'], [
            'data' => $field['data'] ?? null,
            'options' => ['placeholder' => $field['placeholder'] ?? ''],
            'pluginOptions' => $field['pluginOptions'] ?? [],
        ])->label($field['label']);
    } else {
        // Вариант с input
        echo $form->field($model, $field['attribute'])->textInput([
            'maxlength' => $field['maxlength'] ?? true,
            'id' => $field['id'] ?? null,
            'autocomplete' => $field['autocomplete'] ?? 'on',
        ])->label($field['label']);
    }
?>
