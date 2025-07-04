<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\rbac\assets;

use yii\web\AssetBundle;

/**
 * author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ModuleAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/rbac/assets/static';

    public $css = [
        'main.css'
    ];
    public $js = [
        'main.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
    public $publishOptions = [
        'forceCopy' => true
    ];
}