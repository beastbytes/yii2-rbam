<?php
/**
 * rbam\_assignments partial view
 *
 * Renders content for role assignments
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
    <p><?= Yii::t('rbam', 'Shows the number of roles &ndash; including default roles &ndash; assigned to users and the number of permissions granted.') ?></p>
    <p><?= Yii::t('rbam', "Click {manage} to manage a user's role assignments.", [
        'manage' => '<span class="action action--manage"></span>'
    ]) ?></p>
<?php Collapsible::end(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => Yii::t('rbam', 'Name'),
            'attribute' => $this->context->module->nameAttribute,
            'contentOptions' => ['scope' => 'row'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'label' => Yii::t('rbam', 'Roles'),
            'value' => function($model, $key, $index, $column) {
                $attribute = $this->context->module->idAttribute;
                return count(Yii::$app->getAuthManager()->getRolesByUser($model->$attribute));
            },
            'contentOptions' => ['class' => 'number'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'label' => Yii::t('rbam', 'Permissions'),
            'value' => function($model, $key, $index, $column) {
                return count(Yii::$app->getAuthManager()->getPermissionsByUser($key));
            },
            'contentOptions' => ['class' => 'number'],
            'headerOptions' => ['scope' => 'col']
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{manage}',
            'buttons' => [
                'manage' => function($url, $model) {
                    return (Html::a(
                        Yii::t('rbam', 'Manage'),
                        ['roles/assignments', 'id' => $model->{$this->context->module->idAttribute}],
                        ['class' => 'action action--manage']
                    ));
                }
            ]
        ]
    ],
    'pager' => $this->context->module->pager,
    'layout' => '{items}{summary}{pager}'
]);
