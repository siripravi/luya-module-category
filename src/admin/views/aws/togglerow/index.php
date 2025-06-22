<?php

use luya\admin\ngrest\aw\CallbackButtonWidget;
use luya\admin\helpers\Angular;

/**
 * ToggleRowActiveWindow Index View.
 *
 * @var \luya\admin\ngrest\base\ActiveWindowView $this
 * @var \luya\admin\ngrest\base\NgRestModel $model
 */

use luya\admin\ngrest\aw\ActiveWindowFormWidget;
use luya\helpers\Json;
?>
<script>
  zaa.bootstrap.register('InlineController', ['$scope', 'AdminToastService', '$filter', '$window', function($scope, AdminToastService, $filter, $window) {
    $scope.message = "";
    $scope.categories = <?= json_encode($treeData); ?>;
    console.log($scope.categories);
    $scope.addToList = function(member) {
      $scope.$parent.sendActiveWindowCallback('add-to-list', {
        member: member
      }).then(function(response) {
        $scope.$parent.reloadActiveWindow();
      });
    };

    $scope.removeSelected = function(member) {

      AdminToastService.confirm('Selected Items will be deleted. Are you sure?', 'Delete Items', function() {
        console.log('The user has clicked yes!');
        this.close();
        $scope.$parent.sendActiveWindowCallback('removeSelected', {
          member: member
        }).then(function(response) {
          $scope.$parent.toast.info("Deleting Selected items...");
          $scope.$parent.reloadActiveWindow();
          $window.location.reload();
        });
      });
    };
  }]);
</script>
<div class="container" ng-controller="InlineController">
  <div class="row">
    <div class="col-sm-6">
      <h2>Categories</h2>
      <?php echo Angular::directive('tree-select', [
        'category' => $itemId,
        'model' => 'data.update.selected', 'tree' => 'categories'
      ]);  ?>
    </div>
    <div class="col-sm-6 other">
      <h2>Selected {{(data.update.selected | selected).length}} Items</h2>

      <table class="table table-stripped">
        <thead>
          <tr>
            <th style="width:70%">Name</th>
            <th ng-show="(data.update.selected | selected).length > 0"> <button class="btn btn-warning" ng-click="removeSelected(data.update.selected | objToArray)">Delete All</button>
            </th>
          </tr>
        </thead>
        <tbody>

          <tr ng-repeat="(k,item) in data.update.selected | selected track by $index">

            <td>
              <p ng-click="$parent.selectedIndex = $index"> {{item}}</p>
            </td>
            <td></td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>

</div>