<?php
/**
 * URL Rules for RBAM module
 *
 * @copyright Copyright &copy; 2020 BeastBytes - All Rights Reserved
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link https://www.yiiframework.com/
 * @package BeastBytes\Rbam
 */

return [
    new yii\web\GroupUrlRule([
        'prefix' => 'rbam',
        'rules'  => [
            '/' => 'rbam/index',

            'role/assignments/<id:(\d+)>' => 'roles/assignments',
            '<action:(assign-roles|revoke-roles)>/<id:(\d+)>' => 'roles/<action>',
            '<action:(assign-users|revoke-users)>/<name:(.+)>' => 'roles/<action>',
            '<action:create>-<item:(permission|role|rule)>' => '<item>s/<action>',
            '<action:remove>-<items:(permissions|roles|rules)>' => '<items>/<action>',
            '<action:(add-children|manage|remove-children|update)>/<name:(.+)>/<item:(permission|role|rule)>' => '<item>s/<action>',
        ]
    ])
];
