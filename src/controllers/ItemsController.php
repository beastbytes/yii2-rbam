<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\controllers;

use Yii;
use yii\base\Exception;
use yii\rbac\Item;
use BeastBytes\Rbam\base\Controller;
use BeastBytes\Rbam\models\ItemForm;

/**
 * ItemsController Class
 *
 * Base class for RBAC Roles and Permissions
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
abstract class ItemsController extends Controller
{
    abstract public function actionAddChildren($name);
    abstract public function actionCreate();
    abstract public function actionRemoveChildren($name);
    abstract public function actionRemove();
    abstract public function actionUpdate($name);

    /**
     * Adds child items to the parent item
     *
     * @param Item $parent Parent item
     */
    protected function addChildren($parent)
    {
        $am = Yii::$app->getAuthManager();
        $exists = $failure = $loops = $success = [];

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            $child = $am->getPermission($name);
            if ($child === null) {
                $child = $am->getRole($name);
            }

            if ($am->canAddChild($parent, $child)) {
                try {
                    if ($am->addChild($parent, $child)) {
                        $success[] = $name;
                    } else {
                        $failure[] = $name;
                    }
                } catch (Exception $exception) {
                    $exists[] = $name;
                }
            } else {
                $loops[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Items added as children of {parent}: {items}',
                ['parent' => $parent->name, 'items' => join(', ', $success)]
            ));
        }

        if (!empty($failure)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Items not added as children of {parent}: {items}',
                ['parent' => $parent->name, 'items' => join(', ', $failure)]
            ));
        }

        if (!empty($loops)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Items not added as children of {parent} because of loop creation: {items}',
                ['parent' => $parent->name, 'items' => join(', ', $loops)]
            ));
        }

        if (!empty($exists)) {
            Yii::$app->session->addFlash('information', Yii::t(
                'rbam',
                'Items already children of {parent}: {items}',
                ['parent' => $parent->name, 'items' => join(', ', $exists)]
            ));
        }
    }

    /**
     * Removes child items from the parent item
     *
     * @param Item $parent Parent item
     */
    protected function removeChildren($parent)
    {
        $am = Yii::$app->getAuthManager();
        $failure = $success = [];

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            $child = $am->getPermission($name);
            if ($child === null) {
                $child = $am->getRole($name);
            }

            if ($am->removeChild($parent, $child)) {
                $success[] = $name;
            } else {
                $failure[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Items removed as children of {parent}: {items}',
                ['parent' => $parent->name, 'items' => join(', ', $success)]
            ));
        }

        if (!empty($failure)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Items not removed as children of {parent}: {items}',
                ['parent' => $parent->name, 'items' => join(', ', $failure)]
            ));
        }
    }

    protected function update($item)
    {
        $oldName = $item->name;
        $model = new ItemForm([
            'name' => $item->name,
            'description' => $item->description,
            'data' => $item->data,
            'ruleName' => $item->ruleName,
            'type' => $item->type,
            'scenario' => $this->action->id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach ($model->getAttributes() as $attribute => $value) {
                $item->$attribute = $value;
            }
            return Yii::$app->getAuthManager()->update($oldName, $item);
        }

        return $this->render('/items/form', compact('model'));

    }
}
