<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\behaviors;

use yii\db\Query;
use yii\db\Expression;
use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

/**
 * DbManagerBehavior provides methods for yii\rbac\DbManager
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class DbManagerBehavior extends BaseManagerBehavior
{
    /**
     * Returns the parents of an item
     *
     * @param string The child name
     * @return Item[] Parents of the item indexed by name
     */
    public function getParents($name)
    {
        $owner = $this->owner;
        $query = (new Query)
            ->select(['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'])
            ->from([$owner->itemTable, $owner->itemChildTable])
            ->where(['child' => $name, 'name' => new Expression('parent')]);

        $parents = [];

        foreach ($query->all($owner->db) as $row) {
            $parents[$row['name']] = $this->populateItem($row);
        }

        return $parents;
    }

    /**
     * @inheritdoc
     */
    public function getAssignmentsByRole($name)
    {
        if (empty($name)) {
            return [];
        }

        $in = [];
        foreach (array_merge(
            $this->getAncestors($name),
            [$name => $this->owner->getRole($name)]
        ) as $role) {
            $in[] = $role->name;
        }

        $query = (new Query)->select('a.*')
            ->from(['a' => $this->owner->assignmentTable])
            ->andWhere(['in', 'a.item_name', $in]);

        $assignments = [];
        foreach ($query->all($this->owner->db) as $row) {
            $assignments[$row['item_name']] = $this->populateAssigment($row);
        }

        return $assignments;
    }

    /**
     * Returns the Items that use the given rule name
     *
     * @return Item[] Items that use the given rule. The array is indexed by item
     * type and the values are an array of those item types indexed by item name
     */
    public function getItemsByRule($name)
    {
        if (empty($name)) {
            return [];
        }

        $query = (new Query)->select('a.*')
            ->from(['a' => $this->owner->itemTable])
            ->andWhere(['a.rule_name' => $name]);

        $items = [];
        foreach ($query->all($this->owner->db) as $row) {
            $items['item_type' == Item::TYPE_PERMISSION ? 'permissions' : 'roles'][$row['name']] = $this->populateItem($row);
        }

        return $items;
    }

    /**
     * Populates an auth assigment with the data fetched from database
     *
     * @param array $row the data from the auth assigment table
     * @return Assignment the populated auth assigment instance
     */
    protected function populateAssigment($row)
    {
        return new Assignment([
            'userId'    => $row['user_id'],
            'roleName'  => $row['item_name'],
            'createdAt' => $row['created_at'],
        ]);
    }

    /**
     * Populates an auth item with the data fetched from database
     * @param array $row the data from the auth item table
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::class : Role::class;

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }

        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ]);
    }
}
