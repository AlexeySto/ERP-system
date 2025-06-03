<?php

namespace app\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EditableFieldsAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/assets/editable-fields/';

    public $js  = [
        'editable-fields.js',
    ];

    public $css  = [
        'editable-fields.css',
    ];

    public $depends = [
        JqueryAsset::class,
        JuiAsset::class,
        Select2aAsset::class,
        MonthSelectAsset::class,
    ];
}