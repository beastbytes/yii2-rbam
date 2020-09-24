<?php
/**
 * rules\form view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use BeastBytes\Rbam\models\RuleForm;

/** @var RuleForm $model The form model */

$this->title = ($model->scenario === RuleForm::SCENARIO_CREATE
    ? Yii::t('rbam', 'Create Rule')
    : Yii::t('rbam', 'Update Rule')
);

$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('rbam', 'RBAC Overview'),
        'url' => ['rbam/index']
    ],
    [
        'label' => Yii::t('rbam', '{name} Rule', ['name' => $model->name]),
        'url' => ['manage/' . $model->name . '/rule']
    ],
    $this->title
];
$form = ActiveForm::begin(['id' => 'rbac-rule-form']);
?>

<h1><?= $this->title ?></h1>
<?= $form->field($model, 'name')->hint(Yii::t('rbam', 'Name of the rule; a valid CamelCased class name')) ?>
<?= $form->field($model, 'code')->textArea()->hint(Yii::t('rbam', 'Code for the Rule::execute() method')) ?>

<div class="form-row buttons">
    <?= Html::submitButton(
        ($model->scenario === RuleForm::SCENARIO_CREATE
            ? Yii::t('rbam', 'Create')
            : Yii::t('rbam', 'Update')
        ),
        ['class' => 'btn btn--submit btn--' . $model->scenario]
    ) ?>
</div>
<?php
ActiveForm::end();
