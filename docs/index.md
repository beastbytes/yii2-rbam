# RBAM Documentation

## Introduction
RBAM (Role Based Access Manager) is a web based UI for Yii's Role Based Access Control (RBAC) system. It allows administrators to add and manage Roles, Permissions, and Rules, and to assign Roles to or revoke Roles from users.

### RBAC Overview
Permissions grant the ability to do something, e.g. create a post, edit a post, delete a post.

Roles collect together Permissions (the Permissions are children of Roles). Users are assigned Roles; assignments grant the Permissions associated with the Roles. Roles can be the child of another Role; assigning the parent Role to a user also assigns the child Role and grants its Permissions to the user.

Permissions can also be the child of another Permission; in this case the child Permission should use a Rule to further refine the Permission. e.g. an administrator may be granted the Permission to edit all posts. To limit other users to editing only their own posts, a child Permission uses a Rule to check that the current user is the creator of the post being edited.

## CSS
RBAM publishes a CSS asset.

All RBAM content is enclosed in a `<div>` element with "rbam" as its id

```php
?>
<div class="rbam" id="rbam">
  <?= $content ?>
</div>
```
This effectively *namespaces* RBAM content and allows CSS to target it:

```css
#rbam .child-selector {
    ...
}
``` 

CSS classes in RBAM use [BEM](http://getbem.com/) naming conventions.

## RBAM UI
The RBAM UI consists of a number of pages that make it easy to manage Roles, Permissions, Rules, and to assign Roles to or revoke Roles from users.
 
### Overview page
This provides an overview of the RBAC system Assignments, Roles, Permissions, and Rules; each is shown in tabs; from here each item can be managed.

#### Assignments tab
This shows users and the number of roles assigned and permissions granted. Click the *Manage* button for a user to manage their assignments.

#### Roles tab
This shows the Roles in the RBAC system, whether a Rule is associated with the Role and if so which, and the number of child Roles and Permissions. Click the *Manage* button for a Role to manage it, click the *Edit* button for a Role to edit it.

To add a new Role click the *Add* button.

#### Permissions
This shows the Permissions in the RBAC system, whether a Rule is associated with the Permission and if so which, and the number of child Permissions. Click the *Manage* button for a Permission to manage it, click the *Edit* button for a Permission to edit it.

To add a new Permission click the *Add* button.

#### Rules
This shows the Rules in the RBAC system. Click the *Edit* button for a Rule to edit it.

To add a new Rule click the *Add* button.

### User Assignments page
This shows Roles assigned to users and unassigned Roles, and Permissions granted by the assigned Roles, and Permissions the user is not currently granted.

Assigned Roles and granted Permissions include those assigned and granted because they are descendants of an assigned Role.

#### Assign a Role to a User
1. click the Unassigned tab
1. check the checkboxes for the Roles to assign
1. click the *Assign* button in the footer

**Note:** Assigning a Role to a user also assigns descendant Roles

#### Revoke a Role from a User
1. click the Assigned tab
1. check the checkboxes for the Roles to revoke
1. click the *Revoke* button in the footer

**Note:** Only Roles directly assigned to a user can be revoked. Default Roles cannot be revoked and Roles assigned through inheritance cannot be directly revoked (see below).

**Note:** Revoking a Role also revokes all descendant Roles.

***Tip:*** To assign a child Role to a user but revoke a parent Role, revoke the parent Role then assign the child Role.

### Role page
This shows details of a Role and its parents and children; Parents and children have either a superscript 'r' if a Role, or a subscript 'p' if a Permission. Click the *Edit* button to edit the Role.

#### Assignments
User assignments for the Role are managed from this page; users assigned this Role, directly or through inheritence, are shown in the Assigned Users tab, users not assigned this Role are shown in the Unassigned Users tab.

##### Assign Users to the Role
1. click the Unassigned Users tab
1. check the checkboxes for the users to assign
1. click the *Assign* button in the footer

**Note:** Users assigned to the Role are also assigned descendant Roles

##### Revoke Users from the Role
1. click the Assigned Users tab
1. check the checkboxes for the users to revoke
1. click the *Revoke* button in the footer

**Note:** Only users directly assigned to the Role can be revoked. If the Rule is a Default Role users cannot be revoked and users that are assigned the Role through inheritance cannot be revoked - they must be revoked from the ancestor Role directly assigned to them.

**Note:** Revoking a user from the Role also revokes all descendant Roles from the user.

#### Child Items
Child items - Roles and Permissions - are managed from this page. Children and Unrelated items are shown, each with Roles and Permissions in separate tabs.

##### Add Child Items
1. in the Unrelated section, click either Roles to add other Roles as child items or Permissions to add Permissions as child items
1. check the checkboxes for the items to add as child items
1. click the *Add* button in the footer to add the selected items as child items

**Note:** Users assigned the Role will also be assigned child Roles added and/or granted child Permissions added

##### Remove Child Items
1. in the Children section, click either Roles to remove Roles or Permissions to remove Permissions
1. check the checkboxes for the items to remove
1. click the *Remove* button in the footer to remove the selected items

**Note:** Users assigned the Role will be revoked from child Roles removed and/or no longer be granted child Permissions removed

### Create/Edit Role page
Add or update details for the Role and if required select a Rule to be associated with it. Click the *Submit* button create/update the Role.

### Permission page
This shows details of a Permission and its parents and children; Parents and children have either a superscript 'r' if a Role, or a subscript 'p' if a Permission; children will always be Permissions. Click the *Edit* button to edit the Permission.

#### Child Permissions
Child Roles and Permissions are manged from this page. Children and Unrelated Permissions are shown.

##### Add Child Permissions
1. in the Unrelated section, check the checkboxes for the Permissions to add as child Permissions
1. click the *Add* button in the footer to add the selected Permissions as child Permissions

**Note:** Users granted the Permission will also be granted child Permissions added

##### Remove Child Permissions
1. in the Children section, check the checkboxes for the Permissions to remove
1. click the *Remove* button in the footer to remove the selected Permissions

**Note:** Users granted the Permission will no longer be granted child Permissions removed

### Create/Edit Permission page
Add or update details for the Permission and if required select a Rule to be associated with it. Click the *Submit* button create/update the Permission.

### Rule page
This shows details of a Rule and the Roles and Permissions that use it. Click the *Edit* button to edit the Rule.

### Create/Edit Rule page
Add or update details for the Rule.

"name" must be in Pascal case, e.g. MyRule.

"code" is the code for the Rule's `execute()` function. The `execute()` function receives:

* int|string $user the current user ID
* Permission|Role $item the Permission or Role that this rule is associated with
* array $params parameters passed to ManagerInterface::checkAccess()/User::can()

Only enter the code for the `execute()` function, do not define the function.

*Correct*
```php
    return isset($params['post']) ? $params['post']->createdBy == $user : false;
```

*Incorrect*
```php
public function execute($user, $item, $params)
{
    return isset($params['post']) ? $params['post']->createdBy == $user : false;
}
```
The code must return a boolean indicating whether the Rule permits the Role or Permission it is associated with.

Click the *Submit* button create/update the Rule. The Rule class is saved in the namespace given in the RBAM module configuration.
