<?php

namespace app\assets;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/assets/moment/';

    public $js  = [
        'moment.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [];

    public $depends = [
    ];
}
