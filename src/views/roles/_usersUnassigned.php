<?php
/**
 * roles\_usersUnassigned partial view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rbac\Role;

/** @var ActiveDataProvider $unassigned Users not assigned the role */
/** @var Role $item The RBAC role */

echo GridView::widget([
    'dataProvider' => $unassigned,
    'columns' => [
        [
            'label' => Yii::t('rbam', 'Name'),
            'value' => function ($model, $key, $index, $column) {
                return ArrayHelper::getValue($model, $this->context->module->nameAttribute);
            },
            'format' => 'html',
            'contentOptions' => ['scope' => 'row'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'footer' => Html::a(
                Yii::t('rbam', 'Assign'),
                ['roles/assign-users', 'name' => $item->name],
                ['class' => 'action action--assign']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ],
    'pager' => $this->context->module->pager,
    'emptyText' => Yii::t('rbam', 'All users have been assigned the {name} Role', [
        'name' => $item->name
    ]),
    'showFooter' => true,
    'showOnEmpty' => false,
    'options' => ['class' => 'grid-view users-unassigned'],
    'layout' => '{items}{summary}{pager}'
]);
