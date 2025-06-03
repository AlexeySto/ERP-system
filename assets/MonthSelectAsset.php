<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MonthSelectAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/month-select';
    public $css = [
        'month-select.css?v=5',
    ];
    public $js = [
        'month-select.js?v=5',
    ];
    public $depends = [
        \app\assets\AppAsset::class,
        \app\assets\AirDatepicker3Asset::class,
        \app\assets\MomentAsset::class,
    ];
}
