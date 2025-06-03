<?php

namespace app\theme\skote\assets;

use yii\web\AssetBundle;

/**
 * Skote asset bundle.
 */
class SkoteAsset extends AssetBundle
{
    public $sourcePath = '@app/theme/skote/assets/skote';
    public $css = [
        'css/bootstrap.min.css',
        'css/icons.min.css',
        'css/app.min.css',
        'css/wh.css',
        'css/f.css?v=4',
    ];
    public $js = [
        //'js/jquery.min.js',
        'js/bootstrap.bundle.min.js',
        'js/app.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        //'yii\bootstrap5\BootstrapPluginAsset',
    ];

    public function init()
    {
        parent::init();
        $this->publishOptions['forceCopy'] = true;
    }
}
