<?php

namespace App\Enums;

enum UserRole: string{

    case SuperAdmin = "Super Admin";
    case Admin = "Admin";
    case Employee = "Employee";
}
