<?php
/**
 * roles\_permissionsNotGranted partial view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/** @var ArrayDataProvider $notGranted Permissions not granted to the user */

echo GridView::widget([
    'dataProvider' => $notGranted,
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
    ],
    'emptyText' => Yii::t('rbam', 'All permissions have been granted'),
    'pager' => $this->context->module->pager,
    'showOnEmpty' => false,
    'options' => ['class' => 'grid-view permissions-not-granted'],
    'layout' => '{items}{summary}{pager}'
]);
