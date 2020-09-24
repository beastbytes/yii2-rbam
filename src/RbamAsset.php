<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam;

use yii\web\AssetBundle;

/**
 * Asset bundle for Rbam
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class RbamAsset extends AssetBundle
{
	public $basePath = '@webroot';
    public $css = ['rbam.css'];

    public function init()
    {
		$this->sourcePath = __DIR__ . '/assets';
    }
}
