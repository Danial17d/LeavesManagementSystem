<?php

namespace App\Enum;

enum PermissionType :string
{

    //Role Management
    case RoleList = 'role:list';
    case RoleView = 'role:view';
    case RoleCreate = 'role:create';
    case RoleEdit = 'role:edit';
    case RoleDelete = 'role:delete';


}
