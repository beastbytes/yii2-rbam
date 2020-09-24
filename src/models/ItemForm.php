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
 * ItemForm is the model used create and update authorisation items - roles and permissions
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class ItemForm extends Model
{
    /**
     * @var string The additional data associated with this item
     */
    public $data;
    /**
     * @var string The item description
     */
    public $description;
    /**
     * @var string The name of the item
     */
    public $name;
    /**
     * @var string Name of the rule associated with this item
     */
    public $ruleName;
    /**
     * @var int The type of the item
     */
    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name', 'description', 'data'], 'filter', 'filter' => 'trim'],
            [['ruleName', 'data'], 'safe', 'skipOnEmpty' => true]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'description', 'ruleName', 'data'],
            'update' => ['name', 'description', 'ruleName', 'data']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbam', 'Name'),
            'description' => Yii::t('rbam', 'Description'),
            'rule_name' => Yii::t('rbam', 'Rule Name'),
            'data' => Yii::t('rbam', 'Data'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        foreach (['data', 'ruleName'] as $attribute) {
            if (empty($this->$attribute)) {
                $this->$attribute = null;
            }
        }

        return parent::beforeValidate();
    }
}
