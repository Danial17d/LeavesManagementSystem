<?php

namespace App\Enums;

enum PermissionType :string
{

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */
    case UserList = 'user:list';
    case UserView = 'user:view';
    case UserCreate = 'user:create';
    case UserEdit= 'user:edit';
    case UserDelete = 'user:delete';



    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */

    case RoleList   = 'role:list';
    case RoleView   = 'role:view';
    case RoleCreate = 'role:create';
    case RoleEdit   = 'role:edit';
    case RoleDelete = 'role:delete';
    case RoleAssign = 'role:assign';


    /*
    |--------------------------------------------------------------------------
    | Structure Management
    |--------------------------------------------------------------------------
    */
    case StructureList   = 'structure:list';
    case StructureView   = 'structure:view';
    case StructureCreate = 'structure:create';
    case StructureEdit   = 'structure:edit';
    case StructureDelete = 'structure:delete';
    case StructureAssign = 'structure:assign';
    case StructureMove   = 'structure:move';



    /*
    |--------------------------------------------------------------------------
    | Leave Request Management
    |--------------------------------------------------------------------------
    */
    case LeaveRequestList   = 'leave_request:list';
    case LeaveRequestView   = 'leave_request:view';
    case LeaveRequestCreate = 'leave_request:create';
    case LeaveRequestEdit   = 'leave_request:edit';
    case LeaveRequestDelete = 'leave_request:delete';
    case LeaveRequestRevoke = 'leave_request:revoke';


    /*
    |--------------------------------------------------------------------------
    | Leave Approvals Management
    |--------------------------------------------------------------------------
    */
    case LeaveApprovalList  = 'leave_approval:list';
    case LeaveApprovalView = 'leave_approval:view';
    case LeaveApprovalEdit  = 'leave_approval:update';

    /*
    |--------------------------------------------------------------------------
    | Leave Type Management
    |--------------------------------------------------------------------------
    */
    case LeaveTypeList   = 'leave_type:list';
    case LeaveTypeView   = 'leave_type:view';
    case LeaveTypeCreate = 'leave_type:create';
    case LeaveTypeEdit   = 'leave_type:edit';
    case LeaveTypeDelete = 'leave_type:delete';

    /*
    |--------------------------------------------------------------------------
    | Structure Request Management
    |--------------------------------------------------------------------------
    */
    case StructureRequestList   = 'structure_request:list';
    case StructureRequestView   = 'structure_request:view';
    case StructureRequestCreate = 'structure_request:create';
    case StructureRequestEdit   = 'structure_request:edit';
    case StructureRequestDelete = 'structure_request:delete';
    /*
    |--------------------------------------------------------------------------
    | Calendar Management
    |--------------------------------------------------------------------------
    */
    case CalendarView = 'calendar:view';


    case PayRollCalculate = 'payroll:calculate';
}
