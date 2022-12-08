<?php

namespace siripravi\category\controllers;
use siripravi\category\models\Category;
use luya\helpers\Json;

class CategoryController extends \luya\admin\ngrest\base\Controller {

    public $disablePermissionCheck = true;

    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'siripravi\category\models\Category';
    public $globalButtons = [
        [
            'icon' => 'extension',
            'label' => 'Say Hello',
            'ui-sref' => "default.route({moduleRouteId:'teamadmin', controllerId:'member', actionId:'hello-world'})",
        ]
    ];
    public $renderCrud = ['view' => 'siripravi\category\render\RenderCategoryCrudView',];

     public function actionTree($id = 1) {
        $key = $id;
        $node = Category::find($id)->one();
        if($node->isLeaf()){
            $parent = $node->parent()->one();
            $key = $parent->id;
        }
        $treeData = Json::encode(Category::find()->prepareTreeData($key));
        return $this->render("tree",['treeData' => $treeData]);    }
    public function actionCategoryTree() {
        return $this->render('categorytree');
    }

    public function actionTreeSelect() {
        return $this->render('catreeselect');
    }

}
