<?php
/**
 * items\_detail partial view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\helpers\Html;
use yii\rbac\Item;
use yii\widgets\DetailView;

/** @var Item $item RBAC item */
?>

<div class="item">
    <?= DetailView::widget([
        'model' => $item,
        'attributes' => [
            'name',
            [
                'label' => Yii::t('rbam', 'Type'),
                'value' => ($item->type == Item::TYPE_PERMISSION
                    ? Yii::t('rbam', 'Permission')
                    : Yii::t('rbam', 'Role')
                )
            ],
            'description:text:' . Yii::t('rbam', 'Description'),
            'ruleName:text:' . Yii::t('rbam', 'Rule'),
            'data',
            'createdAt:datetime:' . Yii::t('rbam', 'Created'),
            'updatedAt:datetime:' . Yii::t('rbam', 'Updated')
        ]
    ]) ?>
    <div>
        <?= DetailView::widget([
            'model' => $item,
            'attributes' => [
                [
                    'label' => Yii::t('rbam', 'Parents'),
                    'value' => function ($model) {
                        $parents = Yii::$app->getAuthManager()->getParents($model->name);

                        if (empty($parents)) {
                            return Yii::t('rbam', 'No parents');
                        }

                        $_parents = [];
                        ksort($parents);

                        foreach ($parents as $parent) {
                            $_parents[] = $parent->name . ($parent->type == Item::TYPE_PERMISSION ? '<sub>p</sub>' : '<sup>r</sup>');
                        }

                        return join('<br>', $_parents);
                    },
                    'format' => 'html'
                ],
                [
                    'label' => Yii::t('rbam', 'Children'),
                    'value' => function ($model) {
                        $children = Yii::$app->getAuthManager()->getChildren($model->name);

                        if (empty($children)) {
                            return Yii::t('rbam', 'No children');
                        }

                        $_children = [];
                        ksort($children);

                        foreach ($children as $child) {
                            $_children[] = $child->name . ($child->type == Item::TYPE_PERMISSION ? '<sub>p</sub>' : '<sup>r</sup>');
                        }

                        return join('<br>', $_children);
                    },
                    'format' => 'html'
                ]
            ]
        ]) ?>
    </div>
</div>
