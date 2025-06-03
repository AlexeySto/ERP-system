<?php

namespace app\controllers;

use app\grid\GridModelActionsInterface;
use app\helpers\ClassHelper;
use app\models\BaseActiveRecord;
// use app\rbac\RbacAccessControl;
use app\modules\rbac\models\RbacAccessControl;
use yii\filters\AccessControl;
use app\widgets\ActiveForm;
use app\widgets\Flashes;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * BaseController implements the CRUD actions for selected model.
 */
abstract class BaseController extends RefController
{
    //public $layout = '';

	public $data             = []; // Контейнер для самых разных данных для передачи из controller во view

    public $modelClass       = '';
    public $searchClass      = '';

    public $defaultAction    = 'index';

    public $indexTemplate  = 'index';
    public $viewTemplate   = 'view';
    public $createTemplate = 'edit';
    public $editTemplate   = 'edit';

    public $multipleFormClass = '';

    /**
     * @var integer
     * Number of records on page
     */
    public $recsOnPage = 20;

    public $accessRules = [];


    public function behaviors()
    {
        return [
            'access' => [
                'class' => RbacAccessControl::class,
                'rules' => $this->accessRules
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;

        if ($this->searchClass) {
            $searchModel = new $this->searchClass();
        } else {
            $searchModel = new $this->modelClass();
            $searchModel->scenario = BaseActiveRecord::SCENARIO_SEARCH;
        }

        $dataProvider = $searchModel->search($params, $this->recsOnPage);

        $formModel = null;
        if ($this->multipleFormClass) {
            $formModel = new $this->multipleFormClass;
        }

        if ($this->request->isPost && $formModel) {
            $formModel->load(Yii::$app->request->post());

            if ($this->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($formModel);
            }

            if (!$formModel->validate() || !$formModel->save()) {
                Flashes::setError('Ошибка сохранения.');
                Yii::error('Validation errors: ' . print_r($formModel->getErrors(), true));
            } else {
                $formModel = new $this->multipleFormClass;
                Flashes::setSuccess('Записи добавлены');
            }
        }

        return $this->render($this->indexTemplate, [
            'title'        => $this->sysTitle(),
            'searchModel'  => $searchModel,
            'model'        => $searchModel,
            'dataProvider' => $dataProvider,
            'formModel'    => $formModel,
            'data'         => $this->data,
        ]);
    }

    /**
     * Displays a single model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $model;
        }

        if (!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->actionIndex();
        }

        return $this->render($this->viewTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->newModel();
        $model->scenario = BaseActiveRecord::SCENARIO_INSERT;

        Flashes::clear();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Flashes::setSuccess('Запись добавлена');
            return $this->redirect2Referrer();
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        return $this->render($this->createTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
            'data'  => $this->data,
        ]);
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;

        Flashes::clear();

        if (!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect(['index']);
        }

        if ($model instanceof GridModelActionsInterface && !$model->actionAllowed('edit')) {
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flashes::setSuccess('Запись обновлена');
            return $this->redirect(['index']);
        }

        return $this->render($this->editTemplate, [
            'title' => $this->sysTitle(),
            'model' => $model,
            'data'  => $this->data,
        ]);
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->scenario = BaseActiveRecord::SCENARIO_DELETE;
        if(!isset($model)) {
            Flashes::setError('Запись не найдена');
            return $this->redirect(['index']);
        }
        if ($model instanceof GridModelActionsInterface && !$model->actionAllowed('delete')) {
            return $this->redirect(['index']);
        }

        if($model->delete())
        {
            Flashes::setSuccess('Запись удалена');
        }

        return $this->redirect2Referrer();
    }

    /**
     * Finds the model based on its primary key value.
     * @param integer $id
     * @return Model
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        return $modelClass::findOne($id);
    }

    protected function newModel()
    {
        $modelClass = $this->modelClass;
        return new $modelClass();
    }

    protected function sysTitle()
    {
        return Inflector::camel2words($this->id);
    }

    protected function getTotalsForAllPages(ActiveDataProvider $dataProvider, $fields)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $select = [];
        foreach($fields as $fieldName) {
            $select[] = new Expression('SUM(`' . $fieldName . '`) as `' . $fieldName . '`');
        }
        /* @var ActiveQuery $query */
        $query = clone $dataProvider->query;
        $query->select($select)->asArray(true);
        $result = $query->one();

        return is_array($result) ? $result : [];
    }

    public function actionEditPartial()
    {
        if (!$this->modelClass) {
            return ['message' => 'Не указан modelClass'];
        }

        $postData = Yii::$app->request->post(ClassHelper::getBaseClassName($this->modelClass))[Yii::$app->request->post('editableIndex')];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel(Yii::$app->request->post('editableKey'));
        $model->disableShowErrors();
        $model->scenario = $model::SCENARIO_UPDATE;
        $model->load($postData, '');
        if ($model->save()) {
            return ['output' => $this->outputPartial($postData, $model)];
        } else {
            return ['message' => implode(', ', $model->getErrorSummary(true))];
        }
    }

    protected function outputPartial($data)
    {
        return array_shift($data);
    }

    protected function saveMultiple($formModel)
    {
        if ($formModel->load(Yii::$app->request->post())) {
			if (!$formModel->validate() || !$formModel->save()) {
				Flashes::setError('Ошибка сохранения.');
				Yii::error('Validation errors: ' . print_r($formModel->getErrors(), true));
			}
		}
		return true;
    }
}
