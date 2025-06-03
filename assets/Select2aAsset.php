<?php

namespace app\assets;

use yii\web\AssetBundle;

class Select2aAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/select2a/';

    public $css = [
        'css/select2.min.css',
    ];

    public $js = [
        'js/select2.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
