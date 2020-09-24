<?php
/**
 * items\_children partial view
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

/** @var Item $item Parent RBAC item */
/** @var ArrayDataProvider $items RBAC items */
/** @var string $type Type of RBAC items - permissions or roles */

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
            'class'  => 'yii\grid\CheckboxColumn',
            'footer' => Html::a(
                Yii::t('rbam', 'Remove'),
                [
                    "$type/remove-children",
                    'name' => $item->name
                ],
                ['class' => 'action action--remove']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ]),
    'emptyText' => ($type === 'permissions'
        ? Yii::t('rbam', 'No child permissions')
        : Yii::t('rbam', 'No child roles')
    ),
    'showFooter' => true,
    'showOnEmpty' => false,
    'options' => [
        'class' => 'grid-view children',
        'data-type' => Inflector::singularize($type)
    ],
    'layout' => '{items}{summary}{pager}'
]);
