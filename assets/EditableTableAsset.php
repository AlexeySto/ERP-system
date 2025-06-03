<?php

namespace app\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EditableTableAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/assets/editable-table/';

    public $js  = [
        'editable-table.js',
    ];

    public $depends = [
        JqueryAsset::class,
        JuiAsset::class,
        Select2aAsset::class,
        MonthSelectAsset::class,
    ];
}
