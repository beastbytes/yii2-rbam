<?php
/**
 * permissions\manage view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\rbac\Permission;

/** @var ArrayDataProvider $children Children of $item */
/** @var ArrayDataProvider $unrelated Permissions not children of $item */
/** @var Permission $item RBAC permission */

$this->title = Yii::t('rbam', '{name} Permission', ['name' => $item->name]);

$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('rbam', 'RBAC Overview'),
        'url' => ['rbam/index']
    ],
    $this->title
];
$this->params['item'] = $item;
?>
<div class="flex justify-between items-center">
    <h1><?= $this->title ?></h1>
    <?= Html::a(
        Yii::t('rbam', 'Update'),
        ['permissions/update', 'name'=> $item->name],
        ['class' => 'btn btn--update']
    ) ?>
</div>
<?= $this->render('/items/_detail', compact('item')) ?>
<?= $this->render('/items/_relations', compact('item', 'children', 'unrelated')) ?>
