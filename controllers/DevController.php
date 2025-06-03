<?php

namespace app\controllers;

use app\models\forms\DivisionsForm;
use app\models\Division;
use app\migrations\m250208_213657_insert_some_divisions;

/**
 * Dev Controller
 */
class DevController extends BaseController
{
    public $modelClass = Division::class;
    public $multipleFormClass = DivisionsForm::class;

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';

    /**
     * For development purposes only.
     *
     * @return string
     */
    public function actionDev()
    {
        $m = new m250208_213657_insert_some_divisions;
		return date('Y-m-d H:i:s');
    }
}
