<?php
/**
* @link https://github.com/siripravi/luya-module-category
* @copyright Copyright (c) 2020 Purnachandra Rao Valluri <provdigi@gmail.com>
* @license https://github.com/siripravi/luya-module-category/blob/master/LICENSE
*/
namespace siripravi\category\admin\assets;

use luya\web\Asset;

class CategoryAdminAsset extends Asset {

    public $sourcePath = '@categoryadmin/resources';
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
