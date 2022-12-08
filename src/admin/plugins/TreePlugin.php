<?php

namespace siripravi\category\admin\plugins;

use luya\admin\ngrest\base\Plugin;

class TreePlugin extends Plugin {

    public function renderList($id, $ngModel) {
        $this->createListTag($id,$ngModel);
    }

    public function renderCreate($id, $ngModel) {
        return $this->createFormTag('tree-dropdown', $id, $ngModel, ['class' => "tree-dropdown", 'tree-model' => "treedata", 'category' => 'data.create.id']);
        
    }
    
    public function renderUpdate($id, $ngModel) {
        return $this->createFormTag('tree-dropdown', $id, $ngModel, ['class' => "tree-dropdown", 'tree-model' => "treedata", 'on-selection' => "showSelected(node)", 'model' => $ngModel, 'category' => 'data.update.id']);
    }

    public function serviceData($event) {       
        return [
         
        ];
    }

}