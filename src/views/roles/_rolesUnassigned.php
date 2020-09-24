<?php
/**
 * roles\_rolesUnassigned partial view
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
/** @var ArrayDataProvider $unassigned Roles not assigned to the user */

echo GridView::widget([
    'dataProvider' => $unassigned,
    'columns' => [
        [
            'attribute' => 'name',
            'label' => Yii::t('rbam', 'Name'),
            'contentOptions' => ['scope' => 'row'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'attribute' => 'description',
            'label' => Yii::t('rbam', 'Description'),
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'footer' => Html::a(
                Yii::t('rbam', 'Assign'),
                ['roles/assign-roles', 'id' => $id],
                ['class' => 'action action--assign']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ],
    'emptyText' => Yii::t('rbam', 'All roles have been assigned'),
    'pager' => $this->context->module->pager,
    'showFooter' => true,
    'showOnEmpty' => false,
    'options' => ['class' => 'grid-view roles-unassigned'],
    'layout' => '{items}{summary}{pager}'
]);
