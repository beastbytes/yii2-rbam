<?php
/**
 * layouts\view view
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */

/** @var AssetBundle|bool $assetBundle The asset bundle to register or false to not register an asset bundle */
/** @var string $content Content to render */

use yii\web\AssetBundle;

$assetBundle = $this->context->module->assetBundle;

if ($assetBundle !== false) {
    $assetBundle::register($this);
}

$this->beginContent($this->context->module->appLayout);
?>
<div class="rbam" id="rbam">
    <?= \yii\widgets\Breadcrumbs::widget([
        'links' => $this->params['breadcrumbs'],
        'activeItemTemplate' => '<li class="breadcrumb__item breadcrumb__item--active">{link}</li>',
        'itemTemplate' => '<li class="breadcrumb__item">{link}</li>'
    ]) ?>
    <?= $content ?>
</div>
<?php
$this->endContent();
