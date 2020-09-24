<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\base;

use Yii;
use yii\base\InvalidArgumentException;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;

/**
 * Controller Class
 *
 * Base RBAM Controller
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    /**
     * Loads an authorisation item
     *
     * @param string $name Item name
     * @param string $type Item type
     * @return Permission|Role The Item
     * @throws InvalidArgumentException If invalid type
     * @throws NotFoundHttpException If the authorisation item is not found
     */
    protected function load($name, $type)
    {
        if ($type === 'permission') {
            $item = Yii::$app->getAuthManager()->getPermission($name);
        } elseif ($type === 'role') {
            $item = Yii::$app->getAuthManager()->getRole($name);
        } else {
            throw new InvalidArgumentException(strtr('Invalid authorisation item type "{type}"; must be "permission" or "role', [
                '{type}' => $type
            ]));
        }

        if ($item === null) {
            throw new NotFoundHttpException(strtr('Authorisation {type} "{name}" not found', [
                '{type}' => ucfirst($type),
                '{name}' => $name
            ]));
        }

        return $item;
    }
}
