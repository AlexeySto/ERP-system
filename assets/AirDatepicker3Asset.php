<?php

namespace app\assets;

use yii\web\AssetBundle;

class AirDatepicker3Asset extends AssetBundle
{
    public $sourcePath = '@app/assets/air-datepicker-3/';
    public $css = [
        'air-datepicker.css',
    ];
    public $js  = [
        'air-datepicker.js',
    ];
    public $depends = [
    ];
}
