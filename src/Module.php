<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Module as baseModule;

/**
 * Module Class
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 * @uses urlRules
 */
class Module extends baseModule implements BootstrapInterface
{
    const LOCAL_ASSET_BUNDLE = 'BeastBytes\Rbam\RbamAsset';
    const DB_MANAGER = 'yii\\rbac\\DbManager';

    /**
     * @var string|bool fully qualified classname of the CSS asset bundle to register.
     * Set FALSE not to register an asset bundle.
     * By default the local asset bundle is registered.
     */
    public $assetBundle;
    /**
     * @var string path alias to the application layout
     */
    public $appLayout = '@app/views/layouts/main.php';
    /**
     * @var string default route for the module
     */
    public $defaultRoute = 'rbam';
    /**
     * @var array Internalisation configuration for the module
     * @see $_i18n for default values
     */
    public $i18n = [];
    /**
     * @var string ID attribute in the user class
     */
    public $idAttribute = 'id';
    /**
     * @var string The module layout to use
     */
    public $layout = 'main';
    /**
     * @var string name attribute in the user class
     */
    public $nameAttribute = 'username';
    /**
     * @var array|string ORDER BY clause for users
     */
    public $orderBy = 'username ASC';
    /**
     * @var array pager configuration
     */
    public $pager = [];
    /**
     * @var array|string Protected roles.
     * Protected roles are only visible to and can only be assigned by users that have been assigned the role.
     */
    public $protectedRoles;
    /**
     * @var array Roles for this module.
     */
    public $roles = [];
    /**
     * @var string Namespace of the directory that contains RBAC rules
     */
    public $rulesNamespace;
    /**
     * @var array Condition to use when selecting users
     * @link https://www.yiiframework.com/doc/api/2.0/yii-db-queryinterface#where()-detail
     */
    public $userCondition;
    /**
     * @var string Fully qualified class name of User model
     */
    public $userModel;

    /**
     * @var array Default internationalisation configuration
     */
    private $_i18n = [
        'class'          => 'yii\i18n\PhpMessageSource',
        'sourceLanguage' => 'en-GB',
        'basePath'       => '@rbam/messages',
        'fileMap'        => ['rbam/messages' => 'messages.php']
    ];
    /**
     * @var array Default pager configuration
     */
    private $_pager = [];

    private $_roles = [
        'authObjectManager' => 'authObjectManager',
        'authAssignmentManager' => 'authAssignmentManager'
    ];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules(require 'urlRules.php');
    }

    /**
     * Initializes the module.
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!is_string($this->rulesNamespace)) {
            throw new InvalidConfigException('Module::rulesNamespace must be set');
        }

        parent::init();
        Yii::setAlias('@' . $this->id, __DIR__);

        if ($this->assetBundle === null) {
            $this->assetBundle = self::LOCAL_ASSET_BUNDLE;
        }

        foreach (['i18n', 'pager', 'roles'] as $attribute) {
            $_attribute = "_$attribute";
            $this->$attribute += $this->$_attribute;
        }

        Yii::$app->i18n->translations['rbam'] = $this->i18n;

        if (is_string($this->protectedRoles)) {
            $this->protectedRoles = explode(',', $this->protectedRoles);
            array_walk($this->protectedRoles, 'trim');
        }

        $am = Yii::$app->getAuthManager();
        $am->attachBehavior('RBAM', (get_class($am) === self::DB_MANAGER
            ? __NAMESPACE__ . '\behaviors\DbManagerBehavior'
            : __NAMESPACE__ . '\behaviors\PhpManagerBehavior'
        ));
    }
}
