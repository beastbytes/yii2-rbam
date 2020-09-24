<?php
/**
 * roles\manage view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\rbac\Role;

/** @var ActiveDataProvider $assigned Users assigned the role */
/** @var ActiveDataProvider $unassigned Users not assigned the role */
/** @var ArrayDataProvider[] $children RBAC items that are children of $item */
/** @var ArrayDataProvider[] $unrelated RBAC items that are not children of $item */
/** @var Role $item RBAC role */

$this->title = Yii::t('rbam', '{name} Role', ['name' => $item->name]);

$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('rbam', 'RBAC Overview'),
        'url' => ['rbam/index']
    ],
    $this->title
];
?>

<div class="flex justify-between items-center">
    <h1><?= $this->title ?></h1>
    <?= Html::a(
        Yii::t('rbam', 'Update'),
        ['roles/update', 'name'=> $item->name],
        ['class' => 'btn btn--update']
    ) ?>
</div>
<?= $this->render('/items/_detail', compact('item')) ?>
<?= $this->render('_assignments', compact('item', 'assigned', 'unassigned')) ?>
<?= $this->render('/items/_relations', compact('item', 'children', 'unrelated')) ?>
