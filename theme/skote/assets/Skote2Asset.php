<?php

namespace app\theme\skote\assets;

use yii\web\AssetBundle;

/**
 * Skote asset bundle.
 */
class Skote2Asset extends AssetBundle
{
    public $sourcePath = '@app/theme/skote/assets/skote';
    public $css = [
        'css/bootstrap.min.css',
        'css/icons.min.css',
        'css/app.min.css',
        'css/wh.css',
        'css/f.css?v=5',
    ];
    public $js = [
        //'js/jquery.min.js',
        'js/bootstrap.bundle.min.js',
        'libs/metismenu/metisMenu.min.js',
        'libs/node-waves/waves.min.js',
        'libs/simplebar/simplebar.min.js',
        'js/_app.js',
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
