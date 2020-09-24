<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\controllers;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\rbac\Item;
use yii\rbac\Role;
use yii\web\Response;
use BeastBytes\Rbam\models\ItemForm;

/**
 * RolesController Class manages RBAC Roles
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class RolesController extends ItemsController
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
                    ],
                    [
                        'allow' => true,
                        'actions' => ['assignments'],
                        'roles' => [
                            $this->module->roles['authAssignmentManager']
                        ]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['assignRoles', 'assignUsers', 'revokeRoles', 'revokeUsers'],
                        'roles' => [
                            $this->module->roles['authAssignmentManager']
                        ],
                        'verbs' => ['POST']
                    ]
                ]
            ]
        ];
    }

    /**
     * Create a Role
     *
     * @return string|Response The rendering result or Response object
     */
    public function actionCreate()
    {
        $model = new ItemForm([
            'type' => Item::TYPE_ROLE,
            'scenario' => $this->action->id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $item = new Role([
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
                    Yii::t('rbam', '"{name}" Role created', ['name' => $item->name])
                );
            } else {
                Yii::$app->session->addFlash(
                    'failure',
                    Yii::t('rbam', '"{name}" Role not created', ['name' => $item->name])
                );
            }

            return $this->redirect(['rbam/index']);
        }

        return $this->render('/items/form', compact('model'));
    }

    /**
     * Manage a user's role assignments
     *
     * @param int $id User id
     * @return string The rendering result
     * @throws InvalidConfigException
     */
    public function actionAssignments($id)
    {
        $assigned = Yii::$app->getAuthManager()->getRolesByUser($id);
        $userModel = Yii::createObject(['class' => $this->module->userModel]);
        $user = $userModel::find()->where(compact('id'))->one();

        foreach (array_keys($assigned) as $name) {
            foreach (Yii::$app->getAuthManager()->getDescendants($name) as $descendant) {
                if ($descendant->type == Item::TYPE_ROLE) {
                    $assigned[$descendant->name] = $descendant;
                }
            }
        }

        $unassigned = array_diff_key(Yii::$app->getAuthManager()->getRoles(), $assigned);

        if (is_array($this->module->protectedRoles)) {
            foreach ($this->module->protectedRoles as $protectedRole) {
                if (!Yii::$app->getAuthManager()->checkAccess(Yii::$app->user->id, $protectedRole)) {
                    if (isset($assigned[$protectedRole])) {
                        unset($assigned[$protectedRole]);
                    } elseif (isset($unassigned[$protectedRole])) {
                        unset($unassigned[$protectedRole]);
                    }
                }
            }
        }

        $assigned = new ArrayDataProvider([
            'allModels' => $assigned
        ]);

        $unassigned = new ArrayDataProvider([
            'allModels' => $unassigned
        ]);

        $granted = new ArrayDataProvider([
            'allModels' => Yii::$app->getAuthManager()->getPermissionsByUser($id)
        ]);
        $notGranted = new ArrayDataProvider([
            'allModels' => array_diff_key(Yii::$app->getAuthManager()->getPermissions(), $granted->allModels)
        ]);

        return $this->render(
            'assignments',
            compact('id', 'user', 'assigned', 'unassigned', 'granted', 'notGranted')
        );
    }

    /**
     * Assign roles to a user
     *
     * @param int $id User id
     * @return Response The Response object
     */
    public function actionAssignRoles($id)
    {
        $success = $failure = [];
        $am = Yii::$app->getAuthManager();

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            try {
                $am->assign($am->getRole($name), $id);
                $success[] = $name;
            } catch (Exception $e) {
                $info[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Roles assigned to {user}: {roles}',
                ['user' => $id, 'roles' => join(', ', $success)]
            ));
        }

        if (!empty($info)) {
            Yii::$app->session->addFlash('info', Yii::t(
                'rbam',
                'Roles already assigned to {user}: {roles}',
                ['user' => $id, 'roles' => join(', ', $info)]
            ));
        }

        return $this->redirect(['roles/assignments', 'id' => $id]);
    }

    /**
     * Assign users to a role
     *
     * @param string $name Name of the role to assign
     * @return Response The Response object
     */
    public function actionAssignUsers($name)
    {
        $success = $info = [];
        $am = Yii::$app->getAuthManager();
        $role = $am->getRole($name);

        foreach (Yii::$app->getRequest()->post('users') as $id) {
            try {
                $am->assign($role, $id);
                $success[] = $name;
            } catch (Exception $e) {
                $info[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Users assigned to {role}: {users}',
                ['role' => $name, 'users' => join(', ', $success)]
            ));
        }

        if (!empty($info)) {
            Yii::$app->session->addFlash('info', Yii::t(
                'rbam',
                'Users already assigned to {role}: {users}',
                ['role' => $name, 'users' => join(', ', $info)]
            ));
        }

        return $this->redirect(['roles/manage', 'name' => $name]);
    }

    /**
     * Revoke roles from a user
     *
     * @param int $id User id
     * @return Response The Response object
     */
    public function actionRevokeRoles($id)
    {
        $success = $failure = [];
        $am = Yii::$app->getAuthManager();

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            if ($am->revoke($am->getRole($name), $id)) {
                $success[] = $name;
            } else {
                $failure[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Roles revoked from {user}: {roles}',
                ['roles' => join(', ', $success), 'user' => $id]
            ));
        }

        if (!empty($failure)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Roles not revoked from {name}: {roles}',
                ['roles' => join(', ', $failure), 'user' => $id]
            ));
        }

        return $this->redirect(['roles/assignments', 'id' => $id]);
    }

    /**
     * Revoke users from a role
     *
     * @param string $name name of the role
     * @return Response The Response object
     */
    public function actionRevokeUsers($name)
    {
        $success = $failure = [];
        $am = Yii::$app->getAuthManager();
        $role = $am->getRole($name);

        foreach (Yii::$app->getRequest()->post('users') as $id) {
            if ($am->revoke($role, $id)) {
                $success[] = $id;
            } else {
                $failure[] = $id;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Users revoked from {role}: {success}',
                ['role' => $name, 'success' => join(', ', $success)]
            ));
        }

        if (!empty($failure)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Users not revoked from {role}: {failure}',
                ['role' => $name, 'failure' => join(', ', $failure)]
            ));
        }

        return $this->redirect(['roles/manage', 'name' => $name]);
    }

    /**
     * Removes roles from the RBAC system
     *
     * @return Response The Response object
     */
    public function actionRemove()
    {
        $am = Yii::$app->getAuthManager();
        $success = $failure = [];

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            if ($am->remove($am->getRole($name))) {
                $success[] = $name;
            } else {
                $failure[] = $name;
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash('success', Yii::t(
                'rbam',
                'Roles removed: {success}',
                ['success' => join(', ', $success)]
            ));
        }

        if (!empty($failure)) {
            Yii::$app->session->addFlash('failure', Yii::t(
                'rbam',
                'Roles not removed: {failure}',
                ['failure' => join(', ', $failure)]
            ));
        }

        return $this->redirect(['rbam/index']);
    }

    /**
     * Update a role
     *
     * @param string $name Role name
     * @return string|Response The rendering result or Response object
     */
    public function actionUpdate($name)
    {
        $result = $this->update(Yii::$app->getAuthManager()->getRole($name));

        if (is_bool($result)) {
            if ($result) {
                Yii::$app->session->addFlash(
                    'success',
                    Yii::t('rbam', '"{name}" Role updated', compact('name'))
                );
            } else {
                Yii::$app->session->addFlash(
                    'failure',
                    Yii::t('rbam', '"{name}" Role not updated', compact('name'))
                );
            }

            return $this->redirect(['rbam/index']);
        }

        return $result;
    }

    /**
     * Manage a role
     *
     * @param string $name Role name
     * @return string The rendering result
     * @throws InvalidConfigException
     */
    public function actionManage($name)
    {
        $am = Yii::$app->getAuthManager();
        $item = $am->getRole($name);
        $userIds = [];
        $children = $unrelated = ['roles' => [], 'permissions' => []];

        foreach ($am->getAssignmentsByRole($name) as $assignment) {
            $userIds[] = $assignment->userId;
        }

        $model = Yii::createObject(['class' => $this->module->userModel]);
        $assigned = new ActiveDataProvider([
            'query' => $model->find()->andWhere(['in', $this->module->idAttribute, $userIds])
        ]);

        $unassignedQuery = $model->find()->andWhere(['not in', $this->module->idAttribute, $userIds]);
        if (is_array($this->module->userCondition)) {
            $unassignedQuery = $unassignedQuery->andWhere($this->module->userCondition);
        }
        $unassigned = new ActiveDataProvider([
            'query' => $unassignedQuery
        ]);

        $_unrelated = $am->getPermissions();
        $_unrelated = array_merge($_unrelated, $am->getRoles());
        unset($_unrelated[$name]);

        $_children = $am->getChildren($name);
        $_unrelated = array_diff_key($_unrelated, $am->getAncestors($name), $am->getDescendants($name));
        $_unrelated = array_diff_key($_unrelated, $_children);

        foreach ($_children as $name => $child) {
            $type = ($child->type == Item::TYPE_PERMISSION ? 'permissions' : 'roles');

            $children[$type][$name] = $child;
        }

        foreach ($_unrelated as $name => $u) {
            $type = ($u->type == Item::TYPE_PERMISSION ? 'permissions' : 'roles');

            $unrelated[$type][$name] = $u;
        }

        foreach (['permissions', 'roles'] as $type) {
            $children[$type] = new ArrayDataProvider(['allModels' => $children[$type]]);
            $unrelated[$type] = new ArrayDataProvider(['allModels' => $unrelated[$type]]);
        }

        return $this->render('manage', compact('item', 'assigned', 'unassigned', 'children', 'unrelated'));
    }

    /**
     * Adds child items to the parent role
     *
     * @param string $name Name of parent role
     * @return Response The Response object
     */
    public function actionAddChildren($name)
    {
        $this->addChildren(Yii::$app->getAuthManager()->getRole($name));
        return $this->redirect(['manage', 'name' => $name]);
    }

    /**
     * Removes child items from the parent role
     *
     * @param string $name Name of parent role
     * @return Response The Response object
     */
    public function actionRemoveChildren($name)
    {
        $this->removeChildren(Yii::$app->getAuthManager()->getRole($name));
        return $this->redirect(['manage', 'name' => $name]);
    }
}
