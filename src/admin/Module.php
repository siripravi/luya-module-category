<?php

namespace siripravi\category\admin;

class Module extends \luya\admin\base\Module {

    public $apis = [
        'api-category-category' => 'siripravi\category\admin\apis\CategoryController',
    ];

    public function getMenu() {
        return (new \luya\admin\components\AdminMenuBuilder($this))
                        ->node(self::t('category'), 'category')
                        ->group('Settings')
                        ->itemApi(self::t('Categories'), 'categoryadmin/category/index', 'categories', 'api-category-category');                     
    }

    public function getAdminAssets() {
        return [
            'siripravi\category\admin\assets\CategoryAdminAsset'
        ];
    }

    public static function t($message, array $params = []) {
        return parent::baseT('app', $message, $params);
    }

}