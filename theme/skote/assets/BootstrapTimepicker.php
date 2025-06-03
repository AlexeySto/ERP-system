<?php

namespace app\theme\skote\assets;

use yii\web\AssetBundle;

class BootstrapTimepicker extends AssetBundle
{
    public $sourcePath = '@app/theme/skote/assets/bootstrap-timepicker';
    public $css = [
        'css/bootstrap-timepicker.min.css',
    ];
    public $js = [
        'js/bootstrap-timepicker.min.js',
    ];
    public $depends = [];
}
