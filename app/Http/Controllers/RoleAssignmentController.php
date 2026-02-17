<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleAssignmentController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize(PermissionType::RoleAssign);

        $validated = $request->validate([
            'user_token' => ['required', 'string'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $userId = null;
        try {
           $userId =  decrypt($validated['user_token']);

        }catch(DecryptException $e){
            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.',
            ]);
        }
        $user = User::find($userId);

        if ($user->roles->count() > 3) {
            return back()->withErrors([
                'error' => 'You cannot assign more than 3 roles.',
            ]);
        }

        $user->assignRole($validated['role']);

        return back()->with(['status' => 'Role Assigned']);

    }
}
