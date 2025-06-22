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
    function deduplicateTree(items, seen = {}) {
      const output = [];

      angular.forEach(items, function (item) {
        if (seen[item.key]) return;

        seen[item.key] = true;

        const newItem = angular.copy(item);
        let children = newItem.children;

        if (
          children &&
          typeof children === "object" &&
          !Array.isArray(children)
        ) {
          children = Object.values(children);
        }

        newItem.children = children ? deduplicateTree(children, seen) : [];

        output.push(newItem);
      });

      return output;
    }

    function getOptions(scope, items, level) {
      const optionUL = angular.element(
        `<ul class="${level === 0 ? "root" : ""}"></ul>`
      );

      angular.forEach(items, function (obj) {
        if (obj.is_deleted != 0) return;

        const optionLI = angular.element("<li></li>");
        const optionA = angular.element(
          `<a class='tree_label'>${obj.title}</a>`
        );

        optionA.bind("click", function () {
          scope.childClick(obj);
        });

        optionLI.append(optionA);

        // ✅ Normalize children to array before recursion
        let children = obj.children;
        if (
          children &&
          typeof children === "object" &&
          !Array.isArray(children)
        ) {
          children = Object.values(children); // 🔥 Converts {"1": {...}, "2": {...}} to [{...}, {...}]
        }

        if (Array.isArray(children) && children.length > 0) {
          optionLI.append(getOptions(scope, children, level + 1));
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
          /*  $scope.$watch("category", function (newVal) {
            if (newVal) {
              $scope.getTreeData(newVal);
            }
          });*/
          ctrl.hasSelectedValue = function () {
            return $scope.model > 0;
          };
          ctrl.clearSelection = function () {
            var obj = { title: "Nothing Selected" };
            setSelected($scope, obj);
            $scope.model = {};
          };
          $scope.getTreeData = function (id) {
            id = id || 0;
            $http
              .get("admin/api-category-category/tree?id=" + id)
              .then(function (r) {
                $scope.items = deduplicateTree(r.data);
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
