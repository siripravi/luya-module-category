<?php

namespace siripravi\category\models;

use Yii;
use \yii\helpers\Html;
use \yii\behaviors\SluggableBehavior;
use luya\admin\ngrest\base\NgRestModel;
use siripravi\category\admin\traits\ToggleDeleteTrait;
use creocoder\nestedsets\NestedSetsBehavior;

class Category extends NgRestModel
{
    // use ToggleDeleteTrait;    
    public $position;
    public $selected = [];
    public $status;   //  color;

    public $idAttribute = "id";
    public $nameAttribute = "name";

    public static function tableName()
    {
        return 'category';
    }

    const CREATE_ROOT_NODE = 'saveNode';
    const APPEND_NODE = 'appendTo';
    const PREPEND_NODE = 'prependTo';
    const INSERT_AFTER = 'insertAfter';
    const INSERT_BEFORE = 'insertBefore';
    const TYPE_DEFAULT = 'default';

    function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['restcreate'] = ['name', 'slug', 'position', 'related'];
        $scenarios['restupdate'] = ['name']; //, 'slug', 'position', 'related','selected'];

        return $scenarios;
    }
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    public function behaviors()
    {

        $behaviors = [
            'encode' => [
                'class' => 'luya\behaviors\HtmlEncodeBehavior',
                'attributes' => [
                    'name',
                ],
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'slugAttribute' => 'slug',
            ],
            \yii\behaviors\TimeStampBehavior::className(),
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'root',
                'depthAttribute' => 'level',
                // 'hasManyRoots' => true
            ]
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-category-category';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeInsert']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'eventBeforeUpdate']);
    }

    /**
     * @inheritdoc
     */
    public function eventBeforeUpdate()
    {

        $this->updated_at = time();
    }

    /**
     * @inheritdoc
     */
    public function eventBeforeInsert()
    {

        $this->updated_at = time();
        if (empty($this->created_at)) {
            $this->created_at = time();
        }
    }

    public function extraFields()
    {
        return [
            'field',
            'position',
            'related',
            'selected'
        ];
    }

    public function ngrestExtraAttributeTypes()
    {
        return [
            'position' => [
                // 'class' => \siripravi\category\admin\admin\plugins\RadioList::className(),
                'selectArray',
                'data' => [
                    self::CREATE_ROOT_NODE => 'Make New Root',
                    self::PREPEND_NODE => 'As First Child',
                    self::APPEND_NODE => 'As Last Child',
                    self::INSERT_AFTER => 'As Next [same level]',
                    self::INSERT_BEFORE => 'As Previous [same level]',
                ]
            ],
            'related' => ['class' => \siripravi\category\admin\plugins\TreePlugin::className()],
            'selected' => ['class' => \siripravi\category\admin\plugins\TreeSelectPlugin::className()],

        ];
    }


    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'id'  => 'number',
            'name' => 'text',
            'lft' => 'number',
            'rgt' => 'number',
            'depth' => 'number',
            'level' => 'number',
            'slug' => ['slug', 'listener' => 'name'],
            'is_deleted' => [
                'class' => 'siripravi\category\admin\plugins\ToggleStatus',
                'initValue' => 1,
                'interactive' => true,
                'falseIcon' => 'check',
                'trueIcon' => 'visibility_off',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['id', 'level', 'name', 'is_deleted']],
            [['create'], ['name', 'slug', 'position', 'related']],
            ['delete', false],
            ['update', ['name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'root' => Yii::t('app', 'Tree'),
            'lft' => Yii::t('app', 'Lft'),
            'rgt' => Yii::t('app', 'Rgt'),
            'level' => Yii::t('app', 'Level'),
            'position' => Yii::t('app', 'Position'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'is_deleted' => Yii::t('app', 'Status'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['level, is_deleted', 'safe']
        ];
    }

    public function ngRestFilters()
    {
        return [
            'Deleted' => self::find()->where(['=', 'is_deleted', 1]),
            'Active' => self::find()->where(['=', 'is_deleted', 0]),
        ];
    }

    public static function find()
    {
        return new NestedSetCategoryQuery(get_called_class());
    }

    public function ngRestActiveWindows()
    {
        return [
            [
                'class' => \siripravi\category\admin\aws\ToggleRowActiveWindow::class,

                'label' => '', 'icon' => 'delete_sweep'
            ],
        ];
    }


    public function deleteWithChildren()
    {
        if ($this->getIsNewRecord()) {
            throw new Exception('The node can\'t be deleted because it is new.');
        }
        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node can\'t be deleted because it is already deleted.');
        }
        $db = $this->getDb();
        if ($db->getTransaction() === null) {
            $transaction = $db->beginTransaction();
        }

        try {
            $condition = $db->quoteColumnName($this->leftAttribute) . '>='
                . $this->getOldAttribute($this->leftAttribute) . ' AND '
                . $db->quoteColumnName($this->rightAttribute) . '<='
                . $this->getOldAttribute($this->rightAttribute);
            $condition = $condition . ' AND ' . $db->quoteColumnName('is_deleted') . ' = 0';
            $params = [];
            if ($this->hasManyRoots) {
                $condition .= ' AND ' . $db->quoteColumnName($this->rootAttribute) . '=' . $this->rootAttribute;
            }
            $params['is_deleted'] = 1;
            $result = $this->updateAll($params, $condition) > 0;

            if (!$result) {
                if (isset($transaction)) {
                    $transaction->rollback();
                }

                return false;
            }

            if (isset($transaction)) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollback();
            }

            throw $e;
        }

        return true;
    }

    public function mark()
    {
        /* $arr = [$this->id];
        if($this->isLeaf()){
            $arr[] = $this->parent()->one();
        }
      
        $condition = ['in', 'id', $arr];
        $result = $this->updateAll(['is_deleted'=> 0],$condition) > 0;
        */
        $this->is_deleted = 0;

        return $this->update();
    }

    public function getIsDeletedRecord()
    {
        return ($this->is_deleted == 1);
    }

    public function updateTitle()
    {
        return $this->update();
    }

    public function deleteSelected($arr)
    {

        if ($this->getIsNewRecord()) {
            throw new Exception('The node can\'t be deleted because it is new.');
        }
        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node can\'t be deleted because it is already deleted.');
        }

        $db = $this->getDb();
        //return $arr;
        $condition = ['in', 'id', $arr];

        //$condition = [ 'is_deleted' => 0, 'id' => $arr];
        if ($db->getTransaction() === null) {
            $transaction = $db->beginTransaction();
            try {
                $result = $this->updateAll(['is_deleted' => 1], $condition) > 0;
                if (!$result) {
                    if (isset($transaction)) {
                        $transaction->rollback();
                    }

                    return false;
                }

                if (isset($transaction)) {
                    $transaction->commit();
                }
            } catch (\Exception $e) {
                if (isset($transaction)) {
                    $transaction->rollback();
                }

                throw $e;
            }
        }
    }
}
