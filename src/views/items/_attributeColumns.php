<?php
/**
 * items\_columns partial view
 *
 * Defines attribute columns for RBAC items
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

/** @var string $type Type of RBAC items - permissions or roles */

return [
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
        'attribute' => 'ruleName',
        'label' => Yii::t('rbam', 'Rule'),
        'headerOptions' => ['scope' => 'col']
    ],
    [
        'attribute' => 'data',
        'label' => Yii::t('rbam', 'Data'),
        'headerOptions' => ['scope' => 'col']
    ],
    [
        'label' => Yii::t('rbam', 'Children'),
        'value' => function($model) {
            return count(Yii::$app->getAuthManager()->getChildren($model->name));
        },
        'contentOptions' => ['class' => 'number'],
        'headerOptions' => ['scope' => 'col']
    ],
];
