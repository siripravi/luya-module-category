<?php

namespace siripravi\category\admin\ngrest\nestedset\actions;

use Yii;
use yii\web\ServerErrorHttpException;
use siripravi\category\models\Category;
use luya\helpers\Json;

class UpdateAction extends \luya\admin\ngrest\base\actions\UpdateAction
{
    private function successResponse($model)
    {
        $response = Yii::$app->getResponse();
        $response->setStatusCode(201);
    }

    public function run($id)
    {
        $model = Category::findOne($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $model->scenario = $this->scenario;

        $params = Yii::$app->getRequest()->getBodyParams();
        if (isset($params['selected']) && Json::isJson($params['selected'])) {
            $params['selected'] = Json::decode($params['selected']);
        }

        if (Json::isJson($params)) {
            $params = Json::decode($params);
        }

        $model->load($params, '');

        if (!empty($params['selected'])) {
            return $model->deleteSelected($model->selected);
        } else if (!empty($params['name'])) {
            $model->updateTitle();
            return true;
        }
        unset($params);
        if (($model->is_deleted == 1)  && (!$model->mark())) {

            throw new ServerErrorHttpException("Operation did not work: item not found");
        }
        return true;
    }
}
