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
class DisableDoubleSendingAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/disable-double-sending';
    public $css = [];
    public $js = [
        'disable-double-sending.js?v=3',
    ];
    public $depends = [];
}
