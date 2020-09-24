<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\behaviors;

use yii\rbac\Item;

/**
 * PhpManagerBehavior provides methods for yii\rbac\PhpManager
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
class PhpManagerBehavior extends BaseManagerBehavior
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
        $parents = [];

        foreach ($owner->children as $parentName => $children) {
            foreach (array_keys($children) as $childName) {
                if ($childName === $name) {
                    $parents[$parentName] = $owner->getItem($parentName);
                }
            }
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

        $assignments = [];
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

        $items = [];
        return $items;
    }
}
