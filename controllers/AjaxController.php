<?php


namespace app\controllers;


use app\models\BaseActiveRecord;
use app\widgets\ActiveForm;
use Yii;
use yii\web\Response;

class AjaxController extends BaseController
{
    public function actionEdit($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);
            $model->disableShowErrors();
            $model->scenario = BaseActiveRecord::SCENARIO_UPDATE;

            if ($model->load(Yii::$app->request->post(), '')) {
                $errors = ActiveForm::validate($model);
                if (empty($errors)) {
                    return ['result' => $model->save()];
                } else {
                    return ['result' => false, 'errors' => $errors];
                }
            }
        }
        return parent::actionEdit($id);
    }

    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->newModel();
            $model->disableShowErrors();
            $model->scenario = BaseActiveRecord::SCENARIO_INSERT;

            if ($model->load(Yii::$app->request->post(), '')) {
                $errors = ActiveForm::validate($model);
                if (empty($errors)) {
                    return ['result' => $model->save()];
                } else {
                    return ['result' => false, 'errors' => $errors];
                }
            }
        }
        return parent::actionCreate();
    }

    public function actionDelete($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = $this->findModel($id);;
            $model->scenario = BaseActiveRecord::SCENARIO_DELETE;

            if ($model->delete()) {
                return ['result' => true];
            } else {
                return ['result' => false];
            }

        }
        return parent::actionDelete($id);
    }
}