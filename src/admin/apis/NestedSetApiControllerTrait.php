<?php
/**
* @link https://github.com/siripravi/luya-module-category
* @copyright Copyright (c) 2020 Purnachandra Rao Valluri <provdigi@gmail.com>
* @license https://github.com/siripravi/luya-module-category/blob/master/LICENSE
*/

namespace siripravi\category\admin\apis;

trait NestedSetApiControllerTrait
{
    public function actions()
    {
        $actions = call_user_func([parent::class,'actions']);
       
        $actions['create'] = [
            'class' => 'siripravi\category\admin\ngrest\nestedset\actions\CreateAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->createScenario,
        ];
        $actions['update'] = [
            'class' => 'siripravi\category\admin\ngrest\nestedset\actions\UpdateAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->updateScenario,
        ];
        $actions['delete'] = [
            'class' => 'siripravi\category\admin\ngrest\nestedset\actions\DeleteAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];
         $actions['index'] = [
            'class' => 'siripravi\category\admin\ngrest\nestedset\actions\IndexAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];
          
        return $actions;
    }
}