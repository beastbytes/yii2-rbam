<?php
/**
 * roles\assignments view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use common\base\ActiveRecord;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use BeastBytes\UiWidgets\Tabs;

/** @var int $id  user id */
/** @var ArrayDataProvider $assigned Roles assigned to the user */
/** @var ArrayDataProvider $unassigned Roles not assigned to the user */
/** @var ArrayDataProvider $granted Permissions granted to the user */
/** @var ArrayDataProvider $notGranted Permissions not granted to the user */
/** @var ActiveRecord $user The user model */

$this->params['id'] = $id;

$this->title = Yii::t('rbam', 'Role Assignments for {name}', [
    'name' => ArrayHelper::getValue($user, $this->context->module->nameAttribute)
]);

$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('rbam', 'RBAC Overview'),
        'url' => ['rbam/index']
    ],
    $this->title
];
?>
<h1><?= $this->title ?></h1>
<section class="roles">
    <h1><?= Yii::t('rbam', 'Roles') ?></h1>
    <?php
    $tabs = [];
    foreach (['assigned', 'unassigned'] as $roles) {
        $view = '_roles' . ucfirst($roles);
        $tabs[] = [
            'label' => ($roles === 'assigned'
                ? Yii::t('rbam', 'Assigned')
                : Yii::t('rbam', 'Unassigned')
            ),
            'content' => $this->render($view, compact($roles, 'id'))
        ];
    }
    echo Tabs::widget(['items' => $tabs])
    ?>
</section>

<section class="permissions">
    <h1><?= Yii::t('rbam', 'Permissions') ?></h1>
    <?php
    $tabs = [];
    foreach (['granted', 'notGranted'] as $permissions) {
        $view = '_permissions' . ucfirst($permissions);
        $tabs[] = [
            'label' => ($permissions === 'granted'
                ? Yii::t('rbam', 'Granted')
                : Yii::t('rbam', 'Not Granted')
            ),
            'content' => $this->render($view, compact($permissions))
        ];
    }
    echo Tabs::widget(['items' => $tabs])
    ?>
</section>

<?php
$this->registerJs('
jQuery("tfoot a").on("click", function(event) {
    var $target = jQuery(event.target);
    var $grid = $target.closest(".grid-view");
    jQuery.post($target.attr("href"), jQuery.param({
        names:$grid.yiiGridView("getSelectedRows")
    }));
    return false;
});
');
