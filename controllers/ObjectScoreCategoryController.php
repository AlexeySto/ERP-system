<?php
declare(strict_types=1);
namespace app\controllers;

use app\models\ObjectScoreCategory;
use himiklab\sortablegrid\SortableGridAction;

class ObjectScoreCategoryController extends AjaxController
{
    public $modelClass = ObjectScoreCategory::class;



    public function actions(): array
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' =>ObjectScoreCategory::className(),
            ],
        ];
    }
}
