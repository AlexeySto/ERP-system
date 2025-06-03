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
class ShiftsReportAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/shifts-report';
    public $css = [
//        'shifts-report.css?v=3',
    ];
    public $js = [
        'shifts-report.js?v=3',
    ];
    public $depends = [
        \app\assets\AppAsset::class,
    ];
}
