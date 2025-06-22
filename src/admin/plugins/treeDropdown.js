zaa.directive("treeDropdown", function () {
  return {
    restrict: "E",
    scope: {
      model: "=",
      category: "=", // category="data.create.id"
      treeModel: "=", // tree-model="treedata"
      fieldid: "@",
      fieldname: "@",
      label: "@",
      i18n: "@",
    },
    template: `
        <div class="dropdown" ng-class="{ open: isOpen }">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" ng-click="toggleTree()">
                {{ selected.title || 'Nothing Selected' }}
            </button>
            <div class="dropdown-menu p-2" style="max-height:300px; overflow:auto;" ng-if="isOpen">
                <div ng-include="'category-tree-node.html'" ng-init="nodes = items"></div>
            </div>
        </div>
        `,
    controller: [
      "$scope",
      "$http",
      "$document",
      function ($scope, $http, $document) {
        $scope.isOpen = false;
        $scope.selected = { title: "Nothing Selected" };
        $scope.items = [];

        $scope.toggleTree = function () {
          $scope.isOpen = !$scope.isOpen;
        };

        $scope.selectNode = function (node) {
          if (node && node.key > 0) {
            $scope.selected = node;
            $scope.model = node.key;
          } else {
            $scope.selected = { title: "Nothing Selected" };
            $scope.model = null;
          }
          $scope.isOpen = false;
        };

        // Handle outside click
        function closeOnOutsideClick(event) {
          if (
            !$scope.$$phase &&
            $scope.isOpen &&
            !event.target.closest(".dropdown")
          ) {
            $scope.$apply(() => {
              $scope.isOpen = false;
            });
          }
        }
        $document.on("click", closeOnOutsideClick);

        $scope.$on("$destroy", () => {
          $document.off("click", closeOnOutsideClick);
        });
        $scope.$watch("category", function (newVal) {
             console.log("Loading with category ID:", $scope.category); // 👀
          if (newVal) {
            loadTreeData(newVal);
          }
        });
        // Fetch tree data when needed
        $scope.$watch("model", function (newVal) {
          if (!newVal || angular.isArray(newVal)) {
            $scope.model = null;
            $scope.selected = { title: "Nothing Selected" };
           
            loadTreeData($scope.category);
          }
        });

        function loadTreeData(id) {
          $http
            .get("admin/api-category-category/tree" + (id || ""))
            .then(function (response) {
              $scope.items = response.data;
            });
        }
      },
    ],
  };
});
