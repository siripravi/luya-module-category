<?php

namespace siripravi\category\admin\render;

use luya\helpers\Json;

class RenderCategoryCrudView extends \luya\admin\ngrest\render\RenderCrudView
{
    /**
     * Need to run custom service
     * 
     * @TODO move config into `getAngularControllerConfig` method
     * @inheritdoc
     */
    public function registerAngularControllerScript()
    {
        $config = $this->getAngularControllerConfig();
        
        $client = 'zaa.bootstrap.register("'.$this->context->config->getHash().'", ["$scope", "$controller" ,function($scope, $controller) {
			$.extend(this, $controller("CrudController", { $scope : $scope }));
			$scope.config = '.Json::htmlEncode($config).'
         }])';  
        $this->registerJs($client, self::POS_BEGIN);
    }
}