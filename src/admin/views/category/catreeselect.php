
<!--
	https://jsfiddle.net/Shitsu/0ga0km99/1/

	ng-if="item.children && item.children.length > 0"
	ng-if="item.children.length"
-->

<div class="wrapper">
  <div class="ck-nested">
    <div ng-class="{opened: item.opened}" ng-include="'table_tree.html'" ng-repeat="(i,item) in categories"></div>
 </div>   

 <script id="table_tree.html" type="text/ng-template">
<div ng-class="{parent: item.children}" 
		ng-init="parentScope = $parent.$parent; initCheckbox(item, parentScope.item)">
      <div class="indent" style="padding-left: {{28*item.level}}px" ng-click="item.opened = !item.opened">
      </div>
      <span ng-if="item.is_deleted == 1">
            <i class="material-icons ng-scope" style="cursor:pointer;color:red"
             ng-click="toggleStatus($event,item)"  >visibility_off</i>
            {{item.title}}
      </span> 
      <span ng-if="item.is_deleted == 0">
      <div  class="cell-input ui checkbox" 
            ng-click="item.selected = !item.selected; 
            toggleCheckbox(item, parentScope)">

        <input  id="item-{{item.key}}" ng-change="toggleCheckbox(item, parentScope)" ng-model="model[item.key]" type="checkbox" />
        <label for="item-{{item.key}}" ></label>
      </div>
      <div class="cell-name" ng-click="item.opened = !item.opened">
        <span class="cell-title" id="lbl-{{item.key}}">{{item.title}}</span>
      </div>
    </span>
 </div>
    <div class="children" >
      <div ng-class="{opened: item.opened}" ng-include="'table_tree.html'" ng-init="level = level + 1" ng-repeat="(i,item) in item.children"></div>
    </div>

</script>