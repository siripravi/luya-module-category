<?php

namespace siripravi\category\admin\apis;

use siripravi\category\models\Category;

class CategoryController extends \luya\admin\ngrest\base\Api {

    use NestedSetApiControllerTrait;

    public $modelClass = 'siripravi\category\models\Category';

    public function actionTree($id = 0) {

        $key = $id;
        $node = Category::find($id)->one();
     /*   if($node && $node->isLeaf()){
            $parent = $node->parent()->one();
            $key = $parent->id;
        }*/
        
        return ($res = Category::find()->prepareTreeData($key));
    }

    protected function prepareItems($activeQuery) {

        $items = [];
        foreach ($activeQuery->all() as $model) {
            $name = ArrayHelper::getValue($this->modelOptions, 'name', 'title');
            $items[] = [
                'id' => $model->getPrimaryKey(),
                'content' => (is_callable($name) ? call_user_func($name, $model) : $model->{$name}),
                'children' => $this->prepareItems($model->children(1)),
            ];
        }
        return $items;
    }

}
