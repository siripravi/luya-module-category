angular.module(zaa, ['$compile','$filter']);
zaa.filter('selected_old', ['$filter', function ($filter) {
    return function (files) {
      return $filter('filter')(files,
      {
        selected: true });

    };
    }
]);
zaa.factory('linker', function () {
  var links = {}
  return function (arr, key) {
    var link = links[key] || []
    

    arr.forEach(function (newItem, index) {
      var oldItem = link[index]
      if (!angular.equals(oldItem, newItem))
        link[index] = newItem
    })

    link.length = arr.length

    return links[key] = link
  }
});
zaa.filter('Xselected', ['$filter', function ($filter) {
        return function (files, obj) {
            return $filter('filter')(files, function (value) {
                return obj[value.key];
            });
        };
    }]);

zaa.filter('selected', ['$filter', function ($filter) {
        return function (input) {
            var results = []; 
            for (var key in input) {
                 var lbl = "lbl-"+key;
            
            el = document.getElementById(lbl);
            if( input[key] && (el !== null) && (el.innerText !== "")){
                text = el.innerText;
                results.push(text);
                
            }

        }
        return results;               
     }
       
    }]);

zaa.filter('objToArray',['$filter', function ($filter) {
    return function (input,items) {
       
        var results = [];
      
       for (var key in input) {
            input[key] && results.push(Number(key))
        }
     
        return results;
    }
}]);
zaa.directive("treeSelect", ['$compile','$filter', function ($compile,$filter,$sanitize) {
        function main(scope, element, attrs) {         
       }       
        return {
            restrict: "E",
            scope: {
                'model': '=',
                'category': '=',
                'treeFilter': '=',
                'tree': '='
            },            
            link: main,
            controller: ['$scope', '$http','$filter','AdminToastService', function ($scope, $http,$filter,AdminToastService) {
                $scope.toggleExpand = "";
               
                $scope.categories = $scope.tree; 

                $scope.$watch('tree', function (n, o) {
                    if (angular.isArray(n) || n == undefined) {
                        $scope.model = {};                                      
                        console.log($scope.tree);                         
                        $scope.getTreeData($scope.category);
                    }
                });
                $scope.getTreeData = function (id) {                        
                    $http.get('admin/api-categoryadmin-category/tree?id=' + id).then(function (r) {
                        $scope.categories = r.data;
                        console.log($scope.categories);
                    });
                };
                $scope.initCheckbox = function (item, parentItem) {
                     $scope.model[item.key] = parentItem && $scope.model[parentItem.key] || $scope.model[item.key] || false;
                                     
                     console.log($scope.model.names );
                     return $scope.model[item.key] ;
                };
                $scope.toggleCheckbox = function (item, parentScope) {
                    if (item.children != null) {
                    $scope.$broadcast('changeChildren', item);
                }
                if (parentScope.item != null) {
                    return $scope.$emit('changeParent', parentScope);
                }
            };
            $scope.$on('changeChildren', function (event, parentItem) {
                var child, i, len, ref, results;
                ref = parentItem.children;
                results = [];
               
              angular.forEach(parentItem.children, function (child){
                    child.selected = parentItem.selected;                   
                    $scope.model[child.key] = $scope.model[parentItem.key]; 
                      
                    if (child.children != null) {
                        results.push($scope.$broadcast('changeChildren', child));
                    } else {
                        results.push(void 0);
                    }
                });
                return results;
            });
            return $scope.$on('changeParent', function (event, parentScope) {
                var children = [];
             
                angular.forEach(parentScope.item.children, function (child){
                    children.push(child);
                }); 
                $scope.model[parentScope.item.key] = $filter('selected')(children, $scope.model).length === children.length;
               
                parentScope = parentScope.$parent.$parent;
                if (parentScope.item != null) {
                    return $scope.$broadcast('changeParent', parentScope);
                }
            });

            function checkChildren(nextl) {
                console.log(nextl);
               var i, showNode=false;
               if (nextl) {
                  for (i=0;i< nextl.length;i++) 
                     showNode |= nextl[i].show | checkChildren(nextl[i].children);
               }
               return showNode;
            }
            filter = function(obj) {
                console.log(obj);
                var reg = new RegExp($scope.search, 'i');
                var showNode= !$scope.search || reg.test(obj.title);

                obj.show=showNode;
                return (showNode | checkChildren(obj.children)) ;
            };           

        }] ,
        templateUrl: 'category/category/tree-select'
        
        };
    }]);

