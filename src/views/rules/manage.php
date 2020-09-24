<?php
/**
 * rules\view view
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
use yii\rbac\Item;
use yii\widgets\DetailView;
use BeastBytes\UiWidgets\Tabs;

/** @var yii\rbac\Rule $rule */

$this->title = Yii::t('rbam', '{name} Rule', ['name' => $rule->name]);

$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('rbam', 'RBAC Overview'),
        'url'   => ['rbam/index']
    ],
    $this->title
];
$this->params['rule'] = $rule;
?>

<div class="flex justify-between items-center">
    <h1><?= $this->title ?></h1>
    <?= Html::a(
        Yii::t('rbam', 'Update'),
        ['rules/update', 'name'=> $rule->name],
        ['class' => 'btn btn--update']
    ) ?>
</div>
<section class="rbam__rule">
    <?= DetailView::widget([
        'model' => $rule,
        'attributes' => [
            'name',
            [
                'label' => Yii::t('rbam', 'Code'),
                'value' => nl2br($this->context->getExecuteCode($rule)),
                'format' => 'html'
            ],
            'createdAt:datetime:' . Yii::t('rbam', 'Created'),
            'updatedAt:datetime:' . Yii::t('rbam', 'Updated')
        ]
    ]) ?>
</section>
<section class="rbam__rule-used-by">
    <h1><?= Yii::t('rbam', 'Used By') ?></h1>
    <?php
    $items = [];
    foreach ($usedBy as $type => $models) {
        $items[] = [
            'label' => Yii::t('rbam', ucfirst($type)),
            'content' => GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $models
                ]),
                'columns' => [
                    [
                        'label' => Yii::t('rbam', 'Name'),
                        'value' => function($model, $key, $index, $column)
                        {
                            return Html::a($model->name, [
                                '/'. $this->context->module->id . '/items/manage',
                                'name' => $model->name,
                                'type' => ($model->type == Item::TYPE_PERMISSION
                                    ? 'permission'
                                    : 'role'
                                )
                            ]);
                        },
                        'format' => 'html'
                    ],
                    'description:text:'.Yii::t('rbam', 'Description')
                ],
                'pager' => $this->context->module->pager
            ])
        ];
    }
    ?>
    <?= Tabs::widget(compact('items')) ?>
</section>
