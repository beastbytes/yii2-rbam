<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\controllers;

use Exception;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\web\Response;
use BeastBytes\Rbam\models\ItemForm;

/**
 * PermissionsController Class
 *
 * Manages RBAC Permissions
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class PermissionsController extends ItemsController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => [
                            $this->module->roles['authAssignmentManager'],
                            $this->module->roles['authObjectManager']
                        ],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'manage', 'update'],
                        'roles' => [
                            $this->module->roles['authObjectManager']
                        ],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-children', 'remove', 'remove-children'],
                        'roles' => [
                            $this->module->roles['authObjectManager']
                        ],
                        'verbs' => ['POST']
                    ]
                ]
            ]
        ];
    }

    /**
     * Create an RBAC Permission
     *
     * @return string|Response The rendering result or Response object
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new ItemForm([
            'type' => Item::TYPE_PERMISSION,
            'scenario' => $this->action->id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $item = new Permission([
                'name' => $model->name,
                'description' => $model->description,
                'ruleName' => $model->ruleName,
                'data' => $model->data
            ]);

            try {
                $result = Yii::$app->getAuthManager()->add($item);
            } catch (Exception $exception) {
                $result = false;
            }

            if ($result) {
                Yii::$app->session->addFlash(
                    'success',
                    Yii::t('rbam', '"{name}" Permission created', ['name' => $item->name])
                );
            } else {
                Yii::$app->session->addFlash(
                    'failure',
                    Yii::t('rbam', '"{name}" Permission not created', ['name' => $item->name])
                );
            }

            return $this->redirect(['rbam/index']);
        }

        return $this->render('/items/form', compact('model'));
    }

    /**
     * Removes permissions from the RBAC system
     *
     * @return Response The Response object
     */
    public function actionRemove()
    {
        $am = Yii::$app->getAuthManager();
        $success = $failure = [];

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            if ($am->remove($am->getPermission($name))) {
                $success[] = $name;
            } else {
                $failure[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Permissions removed: {success}',
                ['success' => join(', ', $success)]
            ));
        }

        if (!empty($failure)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Permissions not removed: {failure}',
                ['failure' => join(', ', $failure)]
            ));
        }

        return $this->redirect(['rbam/index']);
    }

    /**
     * Adds child items to the parent permission
     *
     * @param string $name Name of parent permission
     * @return Response The Response object
     */
    public function actionAddChildren($name)
    {
        $this->addChildren(Yii::$app->getAuthManager()->getPermission($name));
        return $this->redirect(['manage', 'name' => $name]);
    }

    /**
     * Removes child items from the parent permission
     *
     * @param string $name Name of parent permission
     * @return Response The Response object
     */
    public function actionRemoveChildren($name)
    {
        $this->removeChildren(Yii::$app->getAuthManager()->getPermission($name));
        return $this->redirect(['manage', 'name' => $name]);
    }

    /**
     * Update a permission
     *
     * @param string $name Role name
     * @return string|Response The rendering result or Response object
     */
    public function actionUpdate($name)
    {
        $result = $this->update(Yii::$app->getAuthManager()->getPermission($name));

        if (is_bool($result)) {
            if ($result) {
                Yii::$app->session->addFlash(
                    'success',
                    Yii::t('rbam', '"{name}" Permission updated', compact('name'))
                );
            } else {
                Yii::$app->session->addFlash(
                    'failure',
                    Yii::t('rbam', '"{name}" Permission not updated', compact('name'))
                );
            }

            return $this->redirect(['rbam/index']);
        }

        return $result;
    }

    /**
     * Manage a Permission
     *
     * @param string $name Permission name
     * @return string The rendering result
     */
    public function actionManage($name)
    {
        $am = Yii::$app->getAuthManager();
        $item = $am->getPermission($name);
        //*
        $ancestors = $am->getAncestors($name);
        $descendants = $am->getDescendants($name);
        $children = $unrelated = ['permissions' => []];

        $_children = $am->getChildren($name);

        $_unrelated = $am->getPermissions();
        unset($_unrelated[$name]);
        $_unrelated = array_diff_key($_unrelated, $ancestors, $descendants);
        ksort($_unrelated);

        foreach ($_children as $name => $c) {
            $children['permissions'][$name] = $c;
        }

        foreach ($_unrelated as $name => $u) {
            $unrelated['permissions'][$name] = $u;
        }

        $children['permissions'] = new ArrayDataProvider(['allModels' => $children['permissions']]);
        $unrelated['permissions'] = new ArrayDataProvider(['allModels' => $unrelated['permissions']]);
        //*/

        return $this->render('manage', compact('item', 'children', 'unrelated'));
    }
}
