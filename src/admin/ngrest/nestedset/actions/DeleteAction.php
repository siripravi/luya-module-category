<?php

namespace siripravi\category\admin\ngrest\nestedset\actions;

use Yii;
use yii\web\ServerErrorHttpException;

class DeleteAction extends \luya\admin\ngrest\base\actions\DeleteAction {
    public function run($id) {
        
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        if (!$model->deleteWithChildren()) {
            if ($model->hasErrors()) {
                Yii::$app->getResponse()->setStatusCode(422);
                $errors = [];
                foreach ($model->getErrors() as $field => $errorMessages) {
                    foreach ($errorMessages as $message) {
                        $errors[] = ['field' => $field, 'message' => $message];
                    }
                }
                return $errors;
            }
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }
}