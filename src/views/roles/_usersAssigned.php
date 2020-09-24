<?php
/**
 * roles\_usersAssigned partial view
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

/** @var ActiveDataProvider $assigned Users assigned the role */
/** @var Role $item The RBAC role */

$this->params['item'] = $item;

echo GridView::widget([
    'dataProvider' => $assigned,
    'columns' => [
        [
            'label' => Yii::t('rbam', 'Name'),
            'value' => function ($model, $key, $index, $column) {
                return ArrayHelper::getValue($model, $this->context->module->nameAttribute);
            },
            'format' => 'html',
            'contentOptions' => function ($model, $key, $index, $column) {
                return (is_null(Yii::$app->getAuthManager()->getAssignment($this->params['item']->name, $key))
                    ? ['class' => 'inherited', 'scope' => 'row']
                    : ['scope' => 'row']
                );
            },
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return (is_null(Yii::$app->getAuthManager()->getAssignment($this->params['item']->name, $key))
                    ? ['disabled' => true]
                    : ['value' => $key]
                );
            },
            'footer' => Html::a(
                Yii::t('rbam', 'Revoke'),
                ['roles/revoke-users', 'name' => $item->name],
                ['class' => 'action action--revoke']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ],
    'pager' => $this->context->module->pager,
    'emptyText' => Yii::t('rbam', 'No users have been assigned the {name} Role', [
        'name' => $item->name
    ]),
    'showFooter' => true,
    'showOnEmpty' => false,
    'options' => ['class' => 'grid-view users-assigned'],
    'layout' => '{items}{summary}{pager}'
]);
