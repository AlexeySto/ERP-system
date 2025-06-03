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
class RowEditAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/row-edit';
    public $js = [
        'row-edit.js',
    ];
    public $depends = [
        \app\assets\AppAsset::class,
    ];
    /**
     * @inheritDoc
     */
    public static function register($view)
    {
        $js = "\r\n".'const baseUrl = "' . \Yii::$app->controller->getUniqueId() . '";' . "\r\n";
        $view->registerJs($js, View::POS_BEGIN);
        return parent::register($view);
    }
}
