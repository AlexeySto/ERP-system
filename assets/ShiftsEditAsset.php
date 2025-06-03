<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use http\Url;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ShiftsEditAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/shifts-edit';
    public $css = [
        'shifts-edit.css?v=8',
    ];
    public $js = [
        'shifts-edit.js?v=8',
    ];
    public $depends = [
        \app\assets\AppAsset::class,
    ];

    /**
     * @inheritDoc
     */
    public static function register($view)
    {
        $js = 'var URL_SHIFT_ORDER_WORKS = "' . \yii\helpers\Url::to(['/shifts/order-works']) . '";' . "\r\n";
        $view->registerJs($js, View::POS_BEGIN);
        return parent::register($view);
    }
}
