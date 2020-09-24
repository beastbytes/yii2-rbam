<?php
/**
 * roles\_rolesAssigned partial view
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

/** @var int $id  user id */
/** @var ArrayDataProvider $assigned Roles assigned to the user */

echo GridView::widget([
    'dataProvider' => $assigned,
    'columns' => [
        [
            'attribute' => 'name',
            'label' => Yii::t('rbam', 'Name'),
            'contentOptions' => function ($model, $key, $index, $column) {
                return (is_null(Yii::$app->getAuthManager()->getAssignment($key, $this->params['id']))
                    ? ['class' => 'inherited', 'scope' => 'row']
                    : ['scope' => 'row']
                );
            },
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'attribute' => 'description',
            'label' => Yii::t('rbam', 'Description'),
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return (is_null(Yii::$app->getAuthManager()->getAssignment($key, $this->params['id']))
                    ? ['disabled' => true]
                    : ['value' => $key]
                );
            },
            'footer' => Html::a(Yii::t('rbam', 'Revoke'), [
                'roles/revoke-roles',
                'id' => $id
            ], ['class' => 'action action--revoke']),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ],
    'emptyText' => Yii::t('rbam', 'No roles assigned'),
    'pager' => $this->context->module->pager,
    'showFooter' => true,
    'showOnEmpty' => false,
    'options' => ['class' => 'grid-view roles-assigned'],
    'layout' => '{items}{summary}{pager}'
]);
