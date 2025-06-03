<?php

namespace app\assets;

use yii\web\AssetBundle;

class ExtValidationAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/extform/';
    public $css = [
    ];
    public $js  = [
        'validation.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
