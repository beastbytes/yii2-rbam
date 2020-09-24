<?php
/**
 * items\_unrelated partial view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\rbac\Item;

/** @var Item $item RBAC item */
/** @var ArrayDataProvider $items RBAC items */
/** @var string $type The type of RBAC items */

echo GridView::widget([
    'dataProvider' => $items,
    'columns' => array_merge(require '_attributeColumns.php', [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{manage}',
            'buttons' => [
                'manage' => function($url, $model) {
                    $modelType = ($model->type == Item::TYPE_PERMISSION ? 'permissions'  : 'roles');
                    return (Html::a(
                        Yii::t('rbam', 'Manage'),
                        [
                            $modelType . '/manage',
                            'name' => $model->name
                        ],
                        ['class' => 'action action--manage']
                    ));
                }
            ]
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'footer' => Html::a(
                Yii::t('rbam', 'Make Child'),
                [
                    ($item->type === Item::TYPE_PERMISSION ? 'permissions' : 'roles') . '/add-children',
                    'name' => $item->name
                ],
                ['class' => 'action action--add']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ]),
    'pager' => $this->context->module->pager,
    'emptyText' => ($type === 'permissions'
        ? Yii::t('rbam', 'No unrelated permissions')
        : Yii::t('rbam', 'No unrelated roles')
    ),
    'showFooter' => true,
    'showOnEmpty' => false,
    'options' => [
        'class'=> 'grid-view unrelated',
        'data-type' => Inflector::singularize($type)
    ],
    'layout' => '{items}{summary}{pager}'
]);
