<?php

namespace app\assets;

use yii\web\AssetBundle;

class ExtFormsInitAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/extform/';
    public $css = [
    ];
    public $js  = [
        'extform.js?v=2',
        'jquery.numeric.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
