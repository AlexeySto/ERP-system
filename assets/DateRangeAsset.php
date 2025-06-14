<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class DateRangeAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/assets/date-range/';

    public $js  = [
        'daterangepicker.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'daterangepicker.css',
    ];

    public $depends = [
        MomentAsset::class,
        JqueryAsset::class,
    ];
}
