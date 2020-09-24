<?php
/**
 * roles\_assignments partial view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ActiveDataProvider;
use yii\rbac\Role;
use BeastBytes\UiWidgets\Tabs;

/** @var ActiveDataProvider $assigned Users assigned the role */
/** @var ActiveDataProvider $unassigned Users not assigned the role */
/** @var Role $item The RBAC role */
?>

<section class="assignments">
    <h1><?= Yii::t('rbam', 'Manage Assignments') ?></h1>
    <?php
    $tabs = [];
    foreach (['assigned', 'unassigned'] as $users) {
        $view = '_users' . ucfirst($users);
        $tabs[] = [
            'label' => ($users === 'assigned'
                ? Yii::t('rbam', 'Assigned Users')
                : Yii::t('rbam', 'Unassigned Users')
            ),
            'content' => $this->render($view, compact($users, 'item'))
        ];
    }
    echo Tabs::widget(['items' => $tabs])
    ?>
</section>

<?php
$this->registerJs('
jQuery(".assign, .revoke").on("click", function(event) {
    var $target = jQuery(event.target);
    var $grid = $target.closest(".grid-view");
    jQuery.post($target.attr("href"), jQuery.param({
        ids: $grid.yiiGridView("getSelectedRows")
    }));
    return false;
});
');
