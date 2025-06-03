<?php

namespace app\widgets;

use app\controllers\BaseController;
use yii\helpers\Html;

class ActiveForm extends \yii\widgets\ActiveForm {

    public $useReferrer = true;
    public $fieldClass = 'app\widgets\ActiveField';

    public function init()
    {
        parent::init();
    }

    public static function begin($config = [])
    {
        $begin = parent::begin($config);

        $useReferrer = $config['useReferrer'] ?? true;
        $method = strtolower($config['method'] ?? 'post');
        if ($useReferrer && $method == 'post') {
            $controller = \Yii::$app->controller;
            if ($controller instanceof BaseController) {
                echo Html::hiddenInput('_referrer', $controller->getReferrer());
            }
        }

        return $begin;
    }

    public function label($text, $for = '', $options = [])
    {
        Html::addCssClass($options, ['widget' => 'input-group-text']);
        return Html::label($text, $for, $options);
    }

    public function multiField($fields, $options = [])
    {
        $data = [];
        foreach($fields as $field) {
            $data[] = (string)$field;
        }
        Html::addCssClass($options, ['widget' => 'input-group']);
        return Html::tag('div', implode("\r\n", $data), $options);
    }
}