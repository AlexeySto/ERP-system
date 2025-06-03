<?php

namespace app\controllers;

use app\models\CashFlowTransactionType;
use app\models\CashFlowTransactionCategory;
use himiklab\sortablegrid\SortableGridAction;

use yii\helpers\ArrayHelper;

/**
 * RawMaterial Controller
 */
class CashFlowTransactionCategoriesController extends AjaxController
{
    public $modelClass = CashFlowTransactionCategory::class;

    public $indexTemplate  = 'index';

    public function actions()
    {
        $actions = parent::actions();
        $actions['sort'] = [
            'class' => SortableGridAction::class,
            'modelName' => $this->modelClass,
        ];

        return $actions;
    }

    public function actionIndex()
    {
        // if (!Yii::$app->request->get(ClassHelper::getBaseClassName($this->modelClass))) {
            // $this->setProductType();
        // }

		$this->data['cft_types'] = ArrayHelper::map(
			CashFlowTransactionType::find()
				->where(['is_deleted' => 0])
				->all(),
			'id',
			'name'
		);

        return parent::actionIndex();
    }
}
