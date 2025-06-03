<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ProductEditAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/product-edit';
    public $css = [
        'product-edit.css?v=4',
    ];
    public $js = [
        'product-edit.js?v=4',
    ];
    public $depends = [
        \app\assets\AppAsset::class,
        JuiAsset::class,
    ];
    /**
     * @inheritDoc
     */
    public static function register($view)
    {
        $js = 'var URL_PROD_WORKS = "' . \yii\helpers\Url::to(['/products/works']) . '";' . "\r\n";
        $view->registerJs($js, View::POS_BEGIN);
        return parent::register($view);
    }
}
