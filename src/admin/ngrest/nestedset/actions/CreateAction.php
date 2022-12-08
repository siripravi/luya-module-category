<?php

namespace siripravi\category\admin\ngrest\nestedset\actions;

use Yii;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use siripravi\category\models\Category;

class CreateAction extends \luya\admin\ngrest\base\actions\CreateAction {

    private function successResponse($model) {
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(201);
        $id = implode(',', array_values($model->getPrimaryKey(true)));
        $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
    }

    public function run() {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $model = new $this->modelClass();
        $body_params = Yii::$app->request->post();
        $model->load($body_params, '');
        $operationItem = "";
        $operation = "";
        if (!empty($body_params['related'])) {
            $operationItem = $body_params['related'];
        }
        if (!empty($body_params['position'])) {
            $operation = $body_params['position'];
            $model->position = $operation;
        }

        if (!$model->validate()) {

            throw new ServerErrorHttpException(current(current($model->getErrors())));
        }
        $class = $this->modelClass;
        $item = $class::findOne($operationItem);

        if ((empty($item)) && ($operation == Category::CREATE_ROOT_NODE)) {
            if ($model->makeRoot()) {
                $this->successResponse($model);
            } else {
                throw new ServerErrorHttpException("Prepend to operation did not succeed");
            }
        } else if (!empty($item)) {

            switch ($operation) {
                case Category::CREATE_ROOT_NODE:
                    if ($model->makeRoot()) {
                        $this->successResponse($model);
                    } else {
                        throw new ServerErrorHttpException("Prepend to operation did not succeed");
                    }
                    break;
                case Category::APPEND_NODE:  // insert of first child
                    if ($model->appendTo($item)) {
                        $this->successResponse($model);
                    } else {
                        throw new ServerErrorHttpException("Prepend to operation did not succeed");
                    }
                    break;
                case Category::PREPEND_NODE:   //insert as last child
                    //   print_r($item->attributes); die;
                    if ($model->prependTo($item)) {
                        $this->successResponse($model);
                    } else {
                        throw new ServerErrorHttpException("Append to operation did not succeed");
                    }
                    break;
                case Category::INSERT_AFTER:   // insert on the same level before the node
                    if ($model->insertAfter($item)) {
                        $this->successResponse($model);
                    } else {
                        throw new ServerErrorHttpException("Insert before operation did not succeed");
                    }
                    break;
                case Category::INSERT_BEFORE:  //insert on the same level after the node
                    if ($model->insertBefore($item)) {
                        $this->successResponse($model);
                    } else {
                        throw new ServerErrorHttpException("Insert after operation did not succeed");
                    }
                    break;
                default:
                    throw new ServerErrorHttpException("Operation did not work: Operation not found");
                    break;
            }
        } else {
            throw new ServerErrorHttpException("Operation did not work: item not found");
        }
        return $model;
    }

}
