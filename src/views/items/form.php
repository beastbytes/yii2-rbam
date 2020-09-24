<?php
/**
 * items\form view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\helpers\Html;
use yii\rbac\Item;
use yii\widgets\ActiveForm;
use BeastBytes\Rbam\models\ItemForm;

/** @var ItemForm $model The form model */

$this->title = ($model->scenario === ItemForm::SCENARIO_CREATE
    ? ($model->type == Item::TYPE_PERMISSION
        ? Yii::t('rbam', 'Create Permission')
        : Yii::t('rbam', 'Create Role')
    )
    : ($model->type == Item::TYPE_PERMISSION
        ? Yii::t('rbam', 'Update Permission')
        : Yii::t('rbam', 'Update Role')
    )
);

$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('rbam', 'RBAC Overview'),
        'url' => ['rbam/index']
    ],
    [
        'label' => ($model->type == Item::TYPE_PERMISSION
            ? Yii::t('rbam', '{name} Permission', ['name' => $model->name])
            : Yii::t('rbam', '{name} Role', ['name' => $model->name])
        ),
        'url' => ['manage/' . $model->name . ($model->type == Item::TYPE_PERMISSION ? '/permission' : '/role')]
    ],
    $this->title
];

$form = ActiveForm::begin(['id' => 'rbac-item-form']);
$ruleNames = array_keys(Yii::$app->getAuthManager()->getRules());
?>

<h1><?= $this->title ?></h1>
<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'description') ?>
<?php if (!empty($ruleNames)): ?>
    <?= $form->field($model, 'ruleName')->dropDownList(
        array_combine($ruleNames, $ruleNames), [
            'prompt' => Yii::t('rbam', 'Select Rule')
        ]
    ) ?>
<?php else: ?>
    <?= $form->field($model, 'rule_name')->textInput([
        'disabled' => true,
        'value'    => Yii::t('rbam', 'No rules defined')
    ]) ?>
<?php endif; ?>
<?= $form->field($model, 'data')->hint(Yii::t('rbam', 'Available in the rule as $item->data')) ?>

<div class="form-row buttons">
    <?= Html::submitButton(
        ($model->scenario === ItemForm::SCENARIO_CREATE
            ? Yii::t('rbam', 'Create')
            : Yii::t('rbam', 'Update')
        ),
        ['class' => 'btn btn--submit btn--' . $model->scenario]
    ) ?>
</div>
<?php
ActiveForm::end();
