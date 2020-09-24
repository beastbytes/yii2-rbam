<?php
/**
 * rbam\index view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use BeastBytes\UiWidgets\Tabs;

/** @var ArrayDataProvider|null $permissions RBAC permissions */
/** @var ArrayDataProvider|null $roles RBAC roles */
/** @var ArrayDataProvider|null $rules RBAC rules */
/** @var ActiveDataProvider|null $users Users */

$this->title = Yii::t('rbam', 'RBAC Overview');
$this->params['breadcrumbs'] = [$this->title];
?>

<h1><?= $this->title ?></h1>
<?php
$tabs = [];

$tabs[] = [
    'label' => Yii::t('rbam', 'Assignments'),
    'content' => $this->render('_assignments', ['dataProvider' => $users])
];

foreach (['roles', 'permissions'] as $type) {
    $this->params['type'] = $type;
    $tabs[] = [
        'label' => ($type === 'permissions'
            ? Yii::t('rbam', 'Permissions')
            : Yii::t('rbam', 'Roles')
        ),
        'content' => $this->render('_items', ['dataProvider' => $$type, 'type' => $type])
    ];
}

$tabs[] = [
    'label' => Yii::t('rbam', 'Rules'),
    'content' => $this->render('_rules', ['dataProvider' => $rules])
];

echo Tabs::widget(['items' => $tabs]);

$this->registerJs('
jQuery("tfoot a").on("click", function(event) {
    var $target = jQuery(event.target);
    jQuery.post($target.attr("href"), jQuery.param({
        names:$target.closest(".grid-view").yiiGridView("getSelectedRows")
    }));
    return false;
});
');
