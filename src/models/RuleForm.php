<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\models;

use Yii;
use BeastBytes\Rbam\base\Model;

/**
 * RuleForm is the model used create and update authorisation rules
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class RuleForm extends Model
{
    /**
     * @var string Code for the rule execute() method
     */
    public $code;
    /**
     * @var int Rule created timestamp
     */
    public $createdAt;
    /**
     * @var string Name of the rule
     */
    public $name;
    /**
     * @var string Namespace for the rule
     */
    public $namespace;
    /**
     * @var int Rule updated timestamp
     */
    public $updatedAt;

    private $_class;
    private $_prevClass;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string'],
            [['name', 'code'], 'filter', 'filter' => 'trim'],
            [['name'], 'match', 'pattern' => '/([A-Z][a-z0-9])+/']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'code', 'uses'],
            'update' => ['name', 'code', 'uses']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('rbam', 'Code'),
            'name' => Yii::t('rbam', 'Name')
        ];
    }

    /**
     * Initialises the model
     */
    public function init()
    {
        if ($this->scenario === 'update') {
            $this->_prevClass = $this->name;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        $this->_class = $this->name;
        parent::afterValidate();
    }

    /**
     * Returns the rule class
     *
     * @return string The class
     */
    public function getRuleClass()
    {
        return $this->namespace . '\\' . $this->_class;
    }

    /**
     * Saves the rule as a file in the specified namespace
     *
     * @return bool Whether the rule was successfully saved
     */
    public function save()
    {
        $this->updatedAt = time();

        if ($this->scenario === 'create') {
            $this->createdAt = $this->updatedAt;
        }

        $rule =
<<<RULE
<?php
/**
 * {$this->_class} class file
 *
 * This file has been created by the RBAM module
 */
namespace {$this->namespace};

use yii\\rbac\\Item;
use yii\\rbac\\Rule;
/**
 * {$this->_class} class
 */
class {$this->_class} extends Rule
{
    /**
     * @var string name of the rule
     */
    public \$name = '{$this->name}';
    /**
     * @var integer UNIX timestamp representing the rule creation time
     */
    public \$createdAt = {$this->createdAt};
    /**
     * @var integer UNIX timestamp representing the rule updating time
     */
    public \$updatedAt = {$this->updatedAt};

    /**
     * @param string|integer \$user the user ID.
     * @param Item \$item the role or permission that this rule is associated with
     * @param array \$params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute(\$user, \$item, \$params)
    {
        {$this->code}
    }
}

RULE;
        $rulesDir = str_replace('\\', '/', Yii::getAlias('@' . str_replace('\\', '/', $this->namespace)));

        if ($this->scenario === 'update' && $this->_class !== $this->_prevClass) {
            unlink("$rulesDir/{$this->_prevClass}.php");
        }

        if (!is_dir($rulesDir)) {
            mkdir($rulesDir);
            chmod($rulesDir, 0777);
        }

        $fp = fopen("$rulesDir/{$this->_class}.php", 'w');

        if ($fp == false) {
            return false;
        }

        $ret = (bool)fwrite($fp, $rule);
        $ret &= fclose($fp);

        return $ret;
    }
}
