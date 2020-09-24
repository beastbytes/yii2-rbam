<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use BeastBytes\Rbam\base\Controller;
use yii\filters\AccessControl;

/**
 * RbamController Class
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class RbamController extends Controller
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
                        'actions' => ['initialise'],
                        'roles' => ['?'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists RBAC Assignments, Permissions, Roles, Rules
     *
     * @return string The rendering result
     * @throws InvalidConfigException
     * @todo sort users by name
     */
    public function actionIndex()
    {
        if (Yii::$app->getAuthManager()->checkAccess(Yii::$app->user->id, $this->module->roles['authAssignmentManager'])) {
            $userModel = Yii::createObject(['class' => $this->module->userModel]);
            $users = $userModel::find();

            if (is_array($this->module->userCondition)) {
                $users = $users->andWhere($this->module->userCondition);
            }

            $users = new ActiveDataProvider([
                'query' => $users
            ]);
        } else {
            $users = null;
        }

        if (Yii::$app->getAuthManager()->checkAccess(Yii::$app->user->id, $this->module->roles['authObjectManager'])) {
            $permissions = Yii::$app->getAuthManager()->getPermissions();
            ksort($permissions);
            $permissions = new ArrayDataProvider([
                'allModels' => $permissions
            ]);

            $roles = Yii::$app->getAuthManager()->getRoles();
            if (is_array($this->module->protectedRoles)) {
                foreach ($this->module->protectedRoles as $protectedRole) {
                    if (!Yii::$app->getAuthManager()->checkAccess(Yii::$app->user->id, $protectedRole)) {
                        unset($roles[$protectedRole]);
                    }
                }
            }
            ksort($roles);
            $roles = new ArrayDataProvider([
                'allModels' => $roles
            ]);

            $rules = Yii::$app->getAuthManager()->getRules();
            ksort($rules);
            $rules = new ArrayDataProvider([
                'allModels' => $rules
            ]);
        } else {
            $permissions = $roles = $rules = null;
        }

        return $this->render('index', compact('permissions', 'roles', 'rules', 'users'));
    }
}
