<?php

namespace app\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class InputMaskAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/input-mask/';

    public $css = [
    ];

    public $js  = [
        'jquery.inputmask.min.js',
        'bindings/inputmask.binding.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
