<?php

namespace siripravi\category\assets;

use luya\web\Asset;

class CategoryAdminAsset extends Asset {

    public $sourcePath = '@category/resources';
    public $js = [
        'treeDropdown.js',  
        'treeSelect.js',      
       
    ];
    public $css = [     
        'treeview.css',

    ];
    public $depends = [
        'luya\admin\assets\Main',
        
    ];

}
