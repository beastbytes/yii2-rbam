<?php
/**
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 */

namespace BeastBytes\Rbam\behaviors;

use yii\base\Behavior;
use yii\rbac\Item;

/**
 * BaseManagerBehavior is the base class for RBAM Manager behaviors
 *
 * @author Chris Yates
 * @package BeastBytes\Rbam
 */
abstract class BaseManagerBehavior extends Behavior
{
    /**
     * Returns the ancestors of an item
     *
     * @param string The descendant name
     * @return Item[] Ancestors of the item indexed by name
     */
    public function getAncestors($name)
    {
        $ancestors = [];
        $parents = [$name => $name];

        do {
            $parents = $this->parents(array_keys($parents));
            $ancestors = array_merge($ancestors, $parents);
        } while (!empty($parents));

        return $ancestors;
    }

    /**
     * Returns the users that have been assigned the role, directly and inherited
     *
     * @param string Role name
     * @return Item[] Ancestors of the item indexed by name
     */
    abstract public function getAssignmentsByRole($name);

    /**
     * Returns the descendants of an item
     *
     * @param string The ancestor name
     * @return Item[] Descendants of the item indexed by name
     */
    public function getDescendants($name)
    {
        $descendants = [];
        $children = [$name => $name];

        do {
            $children = $this->children(array_keys($children));
            $descendants = array_merge($descendants, $children);
        } while (!empty($children));

        return $descendants;
    }

    /**
     * Gets the children of a set of items
     *
     * @param array $names Names of items
     * @return Item[] Children of the items indexed by name
     */
    private function children($names)
    {
        $children = [];
        foreach ($names as $name) {
            $children = array_merge($children, $this->owner->getChildren($name));
        }
        return $children;
    }

    /**
     * Gets the parents of a set of items
     *
     * @param array $names Names of items
     * @return Item[] Parents of the items indexed by name
     */
    private function parents($names)
    {
        $parents = [];
        foreach ($names as $name) {
            $parents = array_merge($parents, $this->owner->getParents($name));
        }
        return $parents;
    }
}
