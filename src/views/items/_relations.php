<?php
/**
 * items\_relations partial view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ArrayDataProvider;
use yii\rbac\Item;
use BeastBytes\UiWidgets\Tabs;

/** @var Item $item Parent RBAC item */
/** @var ArrayDataProvider[] $children RBAC items that are children of $item */
/** @var ArrayDataProvider[] $unrelated RBAC items that are not children of $item */
?>

<section class="item-relations">
    <h1><?= Yii::t('rbam', 'Manage Child Items') ?></h1>
    <div class="item-relations--children">
        <h2><?= Yii::t('rbam', 'Children') ?></h2>
        <?php
        $tabs = [];
        foreach ($children as $type => $items) {
            $tabs[] = [
                'label' => ($type === 'permissions'
                    ? Yii::t('rbam', 'Permissions')
                    : Yii::t('rbam', 'Roles')
                ),
                'content' => $this->render('_children', compact('item', 'items', 'type'))
            ];
        }
        ?>
        <?= Tabs::widget(['items' => $tabs]) ?>
    </div>

<div class="item-relations--unrelated">
    <h2><?= Yii::t('rbam', 'Unrelated') ?></h2>
    <?php
    $tabs = [];
    foreach ($unrelated as $type => $items) {
        $tabs[] = [
            'label' => ($type === 'permissions'
                ? Yii::t('rbam', 'Permissions')
                : Yii::t('rbam', 'Roles')
            ),
            'content' => $this->render('_unrelated', compact('item', 'items', 'type'))
        ];
    }
    ?>
    <?= Tabs::widget(['items' => $tabs]) ?>
</div>
</section>

<?php
$this->registerJs('
jQuery(".add, .remove").on("click", function(event) {
    var $target = jQuery(event.target);
    var $grid = $target.closest(".grid-view");
    jQuery.post($target.attr("href"), jQuery.param({
        names: $grid.yiiGridView("getSelectedRows")
    }));
    return false;
});
');


