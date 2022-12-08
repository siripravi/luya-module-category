<?php

namespace siripravi\category\aws;

use Yii;
use luya\admin\ngrest\base\ActiveWindow;
use siripravi\category\models\Category;
use luya\helpers\Json;
/**
 * Toggle Row Active Window.
 *
 * File has been created with `aw/create` command. 
 */
class ToggleRowActiveWindow extends ActiveWindow
{
    /**
     * @var string The name of the module where the ActiveWindow is located in order to finde the view path.
     */
    public $module = '@categorytree';

    /**
     * Default label if not set in the ngrest model.
     *
     * @return string The name of of the ActiveWindow. This is displayed in the CRUD list.
     */
    public function defaultLabel()
    {
        return 'Delete Multiple Categories';
    }

    /**
     * Default icon if not set in the ngrest model.
     *
     * @var string The icon name from goolges material icon set (https://material.io/icons/)
     */
    public function defaultIcon()
    {
        return 'extension';    
    }

    /**
     * The default action which is going to be requested when clicking the ActiveWindow.
     * 
     * @return string The response string, render and displayed trough the angular ajax request.
     */
    public function index()
    {
        $data =  (Category::find()->prepareTreeData($this->itemId));
        return $this->render('index', [
            'model' => $this->model,
            'itemId' => $this->itemId,
        'treeData' => $data
        ]);
    }

    public function callbackHelloWorld()
{
    $postdata = file_get_contents("php://input");

    return $postdata;
    return $this->sendSuccess("success");
}

 public function callbackAddToList($member)
{
    return $this->sendSuccess("success: ".$member);
}

public function callbackRemoveSelected($member)
{
    $selected = json_encode($member);
    $model = Category::findOne($this->itemId);   
    
    $model->scenario = "restupdate";   
      
    if(isset($selected) && Json::isJson($selected)){
        $selArr = Json::decode($selected);
    }

    $model->load(['selected'=>$selArr], '');

    if(!empty($selArr)){
        
        return $model->deleteSelected($selArr);
    }
    else if(!empty($model->is_deleted)){
        if(!$model->mark()){
            throw new ServerErrorHttpException("Operation did not work: item not found");
        }
       
        return $this->sendSuccess("success! Records deleted successfully");
    }
    
    return $this->sendError("Sorry! Something went wrong. Retry.");
}
}