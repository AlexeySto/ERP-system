<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AutoListsAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/auto-lists';
    public $js = [
        'auto-lists.js',
    ];
    public $css = [
        'auto-lists.css',
    ];
    public $depends = [
        \app\assets\AppAsset::class,
    ];
}
