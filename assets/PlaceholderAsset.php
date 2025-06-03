<?php

namespace app\assets;

use yii\web\AssetBundle;

class PlaceholderAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/placeholders/';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
