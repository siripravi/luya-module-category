<?php

namespace siripravi\category\models;

use creocoder\nestedsets\NestedSetsQueryBehavior;

/**
 * @author Wanderson Bragança <wanderson.wbc@gmail.com>
 */
class NestedSetCategoryQuery extends \yii\db\ActiveQuery
{

    public function behaviors()
    {
        return [
            [
                'class' => NestedSetsQueryBehavior::class,
            ]
        ];
    }

    public function inPool()
    {
        return 'children';
    }

    /*
     * public function find(){
      return $this->andWhere(['is_deleted' => 0
      ]);
      }
     * 
     */

    public function mark()
    {
        return $this->saveNode(['is_deleted' => 1]);
    }

    public function prepareTreeData($root = 0, $level = null)
    {
        $data = array_values($this->prepareLevelData($root, $level));
        return $this->parseData($data);
    }

    private function prepareLevelData($root = 0, $level = null)
    {
        $res = [];
        if (is_object($root)) {
            // if ($root->is_deleted == 0) {
            $res[$root->{$root->idAttribute}] = [
                'key' => $root->{$root->idAttribute},
                'title' => $root->{$root->nameAttribute},
                'is_deleted' => $root->is_deleted,
                'level'  => $root->level,
                'opened'  => false
            ];
            // }
            if ($level) {
                foreach ($root->children()->all() as $childRoot) {
                    $aux = $this->prepareLevelData($childRoot, $level - 1);

                    if (isset($res[$root->{$root->idAttribute}]['children']) && !empty($aux)) {
                        $res[$root->{$root->idAttribute}]['hasChildren'] = true;
                        $res[$root->{$root->idAttribute}]['children'] += $aux;
                    } elseif (!empty($aux)) {
                        $res[$root->{$root->idAttribute}]['hasChildren'] = true;
                        $res[$root->{$root->idAttribute}]['children'] = $aux;
                    }
                }
            } elseif (is_null($level)) {
                foreach ($root->children()->all() as $childRoot) {
                    $aux = $this->prepareLevelData($childRoot, null);
                    if (isset($res[$root->{$root->idAttribute}]['children']) && !empty($aux)) {
                        $res[$root->{$root->idAttribute}]['hasChildren'] = true;
                        $res[$root->{$root->idAttribute}]['children'] += $aux;
                    } elseif (!empty($aux)) {
                        $res[$root->{$root->idAttribute}]['hasChildren'] = true;
                        $res[$root->{$root->idAttribute}]['children'] = $aux;
                    }
                }
            }
        } elseif (is_scalar($root)) {
            if ($root == 0) {
                foreach ($this->roots()->all() as $rootItem) {
                    if ($level) {
                        $res += $this->prepareLevelData($rootItem, $level - 1);
                    } elseif (is_null($level)) {
                        $res += $this->prepareLevelData($rootItem, null);
                    }
                }
            } else {
                $modelClass = $this->owner->modelClass;
                $model = new $modelClass;
                $root = $modelClass::find()->andWhere([$model->idAttribute => $root])->one();
                if ($root) {
                    $res += $this->prepareLevelData($root, $level);
                }
                unset($model);
            }
        }
        return $res;
    }

    private function parseData(&$data)
    {
        $tree = [];
        foreach ($data as $key => &$item) {
            if (isset($item['children'])) {
                $item['children'] = array_values($item['children']);
                $tree[$key] = $this->prepareLevelData($item['children']);
            }
            //  if ($item['is_deleted'] == 0) {
            $tree[$key] = $item;
            //   }
        }
        return $tree;
    }
}
