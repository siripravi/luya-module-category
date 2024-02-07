angular.module(zaa, ["$compile", "$filter"]);
zaa.filter("selected_old", [
  "$filter",
  function ($filter) {
    return function (files) {
      return $filter("filter")(files, {
        selected: true,
      });
    };
  },
]);
zaa.filter("selected", [
  "$filter",
  function ($filter) {
    return function (files, obj) {
      return $filter("filter")(files, function (value) {
        return obj[value.key];
      });
    };
  },
]);
zaa.filter("objToArray", function () {
  return function (input) {
    var results = [];
    for (var key in input) {
      input[key] && results.push(Number(key));
    }
    return results;
  };
});
zaa.directive("treeDropdown", [
  "$compile",
  function ($compile, $sanitize) {
    function main(scope, element, attrs) {
      var catList = angular.element(element[0].querySelector(".tree"));
      scope.$watchGroup(["items", "selected"], function (n, o, scope) {
        catList.html("");
        var options = getOptions(scope, scope.items, 0);
        catList.append($compile(options)(scope));
      });
      angular.element(document).bind("click", function (event) {
        if (element !== event.target && !element[0].contains(event.target)) {
          scope.$apply(function () {
            scope.isOpen = false;
          });
        }
      });
    }

    function getOptions(scope, items, level) {
      var optionUL = angular.element("<ul class='root'></ul>");
      angular.forEach(items, function (obj) {
        var optionLI = angular.element("<li></li>");
        var someHtml =
          "<input checked='checked' id='menu" +
          obj.key +
          "' type='checkbox'><span class='tree_label' for='menu" +
          obj.key +
          "'><a>" +
          obj.title +
          "</a></span>";
        var optionA = angular.element(someHtml);
        if (obj.is_deleted == 0) {
          optionLI.append(optionA);
          optionA.bind("click", function () {
            scope.childClick(obj);
          });
        }
        if (obj.children) {
          optionLI.append(getOptions(scope, obj.children, level + 1));
        }
        optionUL.append(optionLI);
      });
      return optionUL;
    }

    return {
      restrict: "E",
      scope: {
        model: "=",
        category: "=",
        categories: "=",
      },
      link: main,
      controller: [
        "$scope",
        "$http",
        function ($scope, $http) {
          ctrl = $scope;
          ctrl.isOpen = false;
          ctrl.openTree = function () {
            ctrl.isOpen = ctrl.isOpen ? false : true;
          };
          ctrl.childClick = function (obj) {
            setSelected($scope, obj);
            ctrl.isOpen = false;
            ctrl.$apply();
          };

          $scope.$watch("model", function (n, o) {
            if (n != null && n) {
            }
          });
          $scope.$watch("model", function (n, o) {
            if (angular.isArray(n) || n == undefined) {
              $scope.model = {};
              $scope.selected = { title: "Nothing Selected" };
              $scope.getTreeData(n);
            }
          });
          ctrl.hasSelectedValue = function () {
            return $scope.model > 0;
          };
          ctrl.clearSelection = function () {
            var obj = { title: "Nothing Selected" };
            setSelected($scope, obj);
            $scope.model = {};
          };
          $scope.getTreeData = function (id) {
            $http
              .get("admin/api-category-category/tree?id=" + id)
              .then(function (r) {
                $scope.items = r.data;
              });
          };
          function setSelected(scope, obj) {
            if (obj.key && obj.key > 0) {
              scope.selected = obj;
              scope.model = obj.key;
            } else {
              scope.selected = obj;
            }
          }
        },
      ],
      templateUrl: "categoryadmin/category/category-tree",
    };
  },
]);
