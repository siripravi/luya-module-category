<div class="form-group form-side-by-side" ng-class="{'input--hide-label': i18n}">
    <div class="form-side form-side-label">
        <label class="ng-binding">Related Category</label>
    </div>
    <div class="form-side">
        <div class="zaaselect" ng-class="{'open':isOpen, 'selected':hasSelectedValue()}">
            <div class="zaaselect-selected">
                <span class="zaaselect-selected-text ng-binding" ng-click="openTree()">{{selected.title}}</span>

                <i class="material-icons zaaselect-clear-icon" ng-click="clearSelection()">clear</i>

                <i class="material-icons zaaselect-dropdown-icon" ng-click="openTree()">keyboard_arrow_down</i>
            </div>
            <div class="zaaselect-dropdown ck-nested">
                <div class="zaaselect-overflow tree">

                </div>
            </div>
        </div>
    </div>
</div>