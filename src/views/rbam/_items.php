<?php
/**
 * rbam\_items partial view
 *
 * Renders content for Permissions and Roles
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

/** @var ArrayDataProvider $dataProvider RBAC items */
/** @var string $type Type of RBAC items - permissions or roles */

$help = [
    'permissions' => '<p>' . join('</p><p>', [
        Yii::t('rbam', 'These are the available permissions, their rules, and data, and number of children.'),
        Yii::t('rbam', 'Permissions allow a user to do something, e.g. create or update a post. Permissions are usually children of roles and users are granted a permission by being assigned the associated role. Permissions can be a child of another permission, usually to further refine the parent permission with an associated rule, e.g. to allow a post author to update their own post.'),
        Yii::t('rbam', 'Click {manage} to manage a permission or {update} to edit it.', [
            'manage' => '<span class="action action--manage"></span>',
            'update' => '<span class="action action--update"></span>'
        ])
    ]) . '</p>',
    'roles' => '<p>' . join('</p><p>', [
        Yii::t('rbam', 'These are the available roles, their rules, data, and number of children.'),
        Yii::t('rbam', 'Roles are assigned to users. Assigning a role grants permissions that are children of the role to the user. If an assigned role has child roles the user will also be assigned the child roles and granted their permissions.'),
        Yii::t('rbam', 'Click {manage} to manage a role and its user assignments or {update} to edit it.', [
            'manage' => '<span class="action action--manage"></span>',
            'update' => '<span class="action action--update"></span>'
        ])
    ]) . '</p>',
];
$this->params['type'] = $type;
?>

<?php Collapsible::begin(['options' => ['class' => 'help']]); ?>
<?= $help[$type] ?>
<?php Collapsible::end(); ?>

<?= Html::a(Yii::t('rbam', 'Create'),  ["$type/create"], ['class' => 'btn btn--create']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => array_merge(require __DIR__ . '/../items/_attributeColumns.php', [
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{manage}{update}',
            'buttons' => [
                'manage' => function($url, $model) {
                    return (Html::a(
                        Yii::t('rbam', 'Manage'),
                        [$this->params['type'] . '/manage', 'name' => $model->name],
                        ['class' => 'action action--manage']
                    ));
                },
                'update' => function($url, $model) {
                    return (Html::a(
                        Yii::t('rbam', 'Update'),
                        [$this->params['type'] . '/update', 'name' => $model->name],
                        ['class' => 'action action--update']
                    ));
                }
            ]
        ],
        [
            'class'  => 'yii\grid\CheckboxColumn',
            'footer' => Html::a(
                Yii::t('rbam', 'Remove'),
                ["$type/remove"],
                ['class' => 'action action--remove']
            ),
            'contentOptions' => ['class' => 'text-center'],
            'footerOptions' => ['class' => 'text-center']
        ]
    ]),
    'emptyText' => ($type === 'permissions'
        ? Yii::t('rbam', 'No permissions defined')
        : Yii::t('rbam', 'No roles defined')
    ),
    'pager' => $this->context->module->pager,
    'showFooter' => true,
    'showOnEmpty' => false,
    'layout' => '{items}{summary}{pager}'
]) ?>

