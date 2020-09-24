<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\controllers;

use Exception;
use \ReflectionClass;
use ReflectionException;
use Yii;
use yii\filters\AccessControl;
use yii\rbac\Rule;
use yii\web\Controller;
use BeastBytes\Rbam\models\RuleForm;

/**
 * RulesController Class
 *
 * Manages RBAC Rules
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class RulesController extends Controller
{
    public $enableCsrfValidation = false;

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
                        'actions' => ['create', 'manage', 'update'],
                        'roles' => [
                            $this->module->roles['authObjectManager']
                        ],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove'],
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
     * Create a rule
     *
     * @return string|yii\web\Response The rendering result or Response object
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new RuleForm([
            'namespace' => $this->module->rulesNamespace,
            'scenario'  => $this->action->id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $ruleClass = $model->getRuleClass();
                $rule = new $ruleClass;

                if (Yii::$app->getAuthManager()->add($rule)) {
                    Yii::$app->session->addFlash(
                        'success',
                        Yii::t('rbam', '"{name}" Rule created', ['name' => $model->name])
                    );
                } else {
                    Yii::$app->session->addFlash(
                        'failure',
                        Yii::t('rbam', '"{name}" Rule not created', ['name' => $model->name])
                    );
                }
            } else {
                Yii::$app->session->addFlash(
                    'failure',
                    Yii::t('rbam', '"{name}" Rule file not created', ['name' => $model->name])
                );
            }

            return $this->redirect(['rbam/index']);
        } else {
            return $this->render('form', compact('model'));
        }
    }

    /**
     * Manage an authorisation rule
     *
     * @param string $name Rule name
     * @return string The rendering result
     */
    public function actionManage($name)
    {
        $am = Yii::$app->getAuthManager();
        $rule = $am->getRule($name);
        $usedBy = $am->getItemsByRule($name);
        return $this->render('manage', compact('rule', 'usedBy'));
    }

    /**
     * Removes authorisation rules
     *
     * @return yii\web\Response The Response object
     */
    public function actionRemove()
    {
        $am = Yii::$app->getAuthManager();
        $success = $notDeleted = $notRemoved = [];

        foreach (Yii::$app->getRequest()->post('names') as $name) {
            if (!$am->remove($am->getRule($name))) {
                $notRemoved[] = $name;
            } else {
                if (unlink(str_replace('\\', '/', Yii::getAlias('@' . str_replace('\\', '/', $this->module->rulesNamespace))) . '/' . $name . '.php')) {
                    $success[] = $name;
                } else {
                    $notDeleted[] = $name;
                }
            }
        }

        if (!empty($success)) {
            Yii::$app->session->addFlash(
                'success',
                Yii::t(
                    'rbam',
                    'Rules removed and class files deleted: {success}',
                    ['success' => join(', ', $success)]
                )
            );
        }

        if (!empty($notRemoved)) {
            Yii::$app->session->addFlash(
                'failure',
                Yii::t(
                    'rbam',
                    'Rules not removed: {failure}',
                    ['failure' => join(', ', $notRemoved)]
                )
            );
        }

        if (!empty($notDeleted)) {
            Yii::$app->session->addFlash(
                'failure',
                Yii::t(
                    'rbam',
                    'Rules removed but class files not deleted: {failure}',
                    ['failure' => join(', ', $notDeleted)]
                )
            );
        }

        return $this->redirect(['rbam/index']);
    }

    /**
     * Update a rule
     *
     * @return string|yii\web\Response The rendering result or Response object
     * @throws ReflectionException
     */
    public function actionUpdate($name)
    {
        $oldName = $name;
        $rule = Yii::$app->getAuthManager()->getRule($name);

        $model = new RuleForm([
            'name' => $rule->name,
            'code' => $this->getExecuteCode($rule),
            'createdAt' => $rule->createdAt,
            'updatedAt' => $rule->updatedAt,
            'namespace' => $this->module->rulesNamespace,
            'scenario' => $this->action->id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $ruleClass = $model->getRuleClass();
                $rule = new $ruleClass;

                if (Yii::$app->getAuthManager()->update($oldName, $rule)) {
                    Yii::$app->session->addFlash(
                        'success',
                        Yii::t('rbam', '"{name}" Rule updated', ['name' => $model->name])
                    );
                } else {
                    Yii::$app->session->addFlash(
                        'failure',
                        Yii::t('rbam', '"{name}" Rule not updated', ['name' => $model->name])
                    );
                }
            } else {
                Yii::$app->session->addFlash(
                    'failure',
                    Yii::t('rbam', '"{name}" Rule file not created', ['name' => $model->name])
                );
            }

            return $this->redirect(['rbam/index']);
        }

        return $this->render('form', compact('model'));
    }

    /**
     * Returns the code from the given Rule's execute() method
     *
     * @param Rule The rule
     * @return string The execute method code
     * @throws ReflectionException
     * @throws Exception
     */
    public function getExecuteCode($rule)
    {
        $reflector = new ReflectionClass($rule);
        $executeMethod = $reflector->getMethod('execute');

        $start = $executeMethod->getStartLine() + 1;
        $end = $executeMethod->getEndLine() - 1;

        $rulesDir = str_replace(
            '\\', '/',
            Yii::getAlias('@' . str_replace('\\', '/', $this->module->rulesNamespace))
        );

        if (!is_dir($rulesDir)) {
            throw new Exception('Rules directory does not exist');
        }

        $filename = $rulesDir . '/' . $reflector->getShortName() . '.php';
        $code = array_slice(file($filename), $start, $end - $start);

        foreach ($code as &$line) {
            $line = trim($line);
        }

        return join("\n", $code);
    }
}
