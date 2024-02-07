<?php

use luya\admin\ngrest\aw\CallbackButtonWidget;

/**
 * ToggleRowActiveWindow Index View.
 *
 * @var \luya\admin\ngrest\base\ActiveWindowView $this
 * @var \luya\admin\ngrest\base\NgRestModel $model
 */

use luya\admin\ngrest\aw\ActiveWindowFormWidget;
?>

<script>
    zaa.bootstrap.register('InlineController', ['$scope', '$filter', function($scope, $filter) {
        console.log($scope.parent);
        $scope.seleceds = {};
        $scope.categories = <?= json_encode($data); ?>;
        console.log($scope.categories);
        $scope.initCheckbox = function(item, parentItem) {
            return $scope.seleceds[item.key] = parentItem && $scope.seleceds[parentItem.key] || $scope.seleceds[item.key] || false;
        };
        $scope.toggleCheckbox = function(item, parentScope) {
            if (item.children != null) {
                $scope.$broadcast('changeChildren', item);
            }
            if (parentScope.item != null) {
                return $scope.$emit('changeParent', parentScope);
            }
        };
        $scope.$on('changeChildren', function(event, parentItem) {
            var child, i, len, ref, results;
            ref = parentItem.children;
            results = [];
            // for (i = 0, len = ref.length; i < len; i++) {
            //     child = ref[i];
            angular.forEach(parentItem.children, function(child) {
                child.selected = parentItem.selected;
                $scope.seleceds[child.key] = $scope.seleceds[parentItem.key];

                if (child.children != null) {
                    results.push($scope.$broadcast('changeChildren', child));
                } else {
                    results.push(void 0);
                }
            });
            return results;
        });
        return $scope.$on('changeParent', function(event, parentScope) {
            var children = [];
            //  children = parentScope.item.children;
            angular.forEach(parentScope.item.children, function(child) {
                children.push(child);
            });
            $scope.seleceds[parentScope.item.key] = $filter('selected')(children, $scope.seleceds).length === children.length;
            parentScope = parentScope.$parent.$parent;
            if (parentScope.item != null) {
                return $scope.$broadcast('changeParent', parentScope);
            }
        });
    }]);
</script>

<?php $form = ActiveWindowFormWidget::begin([
    'callback' => 'post-data',
    'buttonValue' => 'Submit',
    'angularCallbackFunction' => 'function() {
                                        console.log("Hi");
                                    };'
]); ?>
<!--?= $form->field('firstname', 'Firstname'); ?-->
<!--?= $form->field('hello')->checkboxList(['1'=>"one", "2" => "Two"]); ?-->

<div id="wrapper" class="container" ng-controller="InlineController">

    {{seleceds | objToArray}}
    <tree family="treeFamily"></tree>
</div>
<?php $form::end(); ?>

<script id="table_tree.html" type="text/ng-template">
    <li ng-class="{parent: item.children}"
    ng-init="parentScope = $parent.$parent; initCheckbox(item, parentScope.item)">
    <span class="xindent" ng-click="item.opened = !item.opened"> </span>
        <span class="cell-title">                
            <input  id="item{{item.key}}"  ng-change="toggleCheckbox(item, parentScope)" ng-model="seleceds[item.key]" type="checkbox" />
            <label for="item{{item.key}}" class="custom-unchecked">
                      {{item.title}}          
            </label>
        </span>
  </li>        
  <ul class="child-list" ng-class="{opened: item.opened}" 
      ng-include="'table_tree.html'" ng-init="level = level + 1" ng-repeat="(i,item) in item.children">
  </ul>
</script>