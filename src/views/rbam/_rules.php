<?php
/**
 * rbam\_rules partial view
 *
 * Renders content for Rules
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
use BeastBytes\UiWidgets\Collapsible;

/** @var ArrayDataProvider $dataProvider RBAC rules */
?>

<?php Collapsible::begin(['options' => ['class' => 'help']]); ?>
    <p><?= Yii::t('rbam', 'These are the available rules.') ?></p>
    <p><?= Yii::t('rbam', 'Rules further define when a role applies to a user or a permission is granted, e.g. a rule may allow users to update only their posts.') ?></p>
    <p><?= Yii::t('rbam', 'Click {manage} to manage a rule or {update} to edit it.', [
        'manage' => '<span class="action action--manage"></span>',
        'update' => '<span class="action action--update"></span>'
    ]) ?></p>
<?php Collapsible::end(); ?>

<?= Html::a(Yii::t('rbam', 'Create'), ['rules/create'], ['class' => 'btn btn--create']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'name',
            'label' => Yii::t('rbam', 'Name'),
            'contentOptions' => ['scope' => 'row'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'attribute' => 'createdAt',
            'format' => 'datetime',
            'label' => Yii::t('rbam', 'Created'),
            'contentOptions' => ['class' => 'datetime'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'attribute' => 'updatedAt',
            'format' => 'datetime',
            'label' => Yii::t('rbam', 'Updated'),
            'contentOptions' => ['class' => 'datetime'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{manage}{update}',
            'buttons' => [
                'manage' => function($url, $model) {
                    return (Html::a(
                        Yii::t('rbam', 'Manage'),
                        ['rules/manage', 'name' => $model->name],
                        ['class' => 'action action--manage']
                    ));
                },
                'update' => function($url, $model) {
                    return (Html::a(
                        Yii::t('rbam', 'Update'),
                        ['rules/update', 'name' => $model->name],
                        ['class' => 'action action--update']
                    ));
                }
            ]
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'footer' => Html::a(
                Yii::t('rbam', 'Remove'),
                ['rules/remove'],
                ['class' => 'action action--remove']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ],
    'emptyText' => Yii::t('rbam', 'No rules defined'),
    'pager' => $this->context->module->pager,
    'showFooter' => true,
    'showOnEmpty' => false,
    'layout' => '{items}{summary}{pager}'
]) ?>
