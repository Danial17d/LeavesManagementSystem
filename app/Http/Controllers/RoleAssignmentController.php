<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleAssignmentController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize(PermissionType::RoleAssign);

        $validated = $request->validate([
            'user_ids' => ['nullable', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'user_token' => ['nullable', 'string'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $userIds = $validated['user_ids'] ?? [];

        if (empty($userIds) && !empty($validated['user_token'])) {
            try {
                $userIds = [(int) decrypt($validated['user_token'])];
            } catch (\Throwable $e) {
                return back()->withErrors([
                    'error' => 'Something went wrong. Please try again.',
                ]);
            }
        }

        if (empty($userIds)) {
            return back()->withErrors([
                'error' => 'Please select at least one user.',
            ]);
        }

        $users = User::query()
            ->with('roles:id,name')
            ->whereIn('id', $userIds)
            ->get();

        $blockedUsers = $users
            ->filter(fn (User $user) => $user->roles->count() >= 3 && !$user->hasRole($validated['role']))
            ->pluck('name')
            ->values();

        if ($blockedUsers->isNotEmpty()) {
            return back()->withErrors([
                'error' => 'Some users already have 3 roles: ' . $blockedUsers->join(', '),
            ]);
        }

        foreach ($users as $user) {
            if (!$user->hasRole($validated['role'])) {
                $user->assignRole($validated['role']);
            }
        }

        return back()->with(['status' => 'Role assigned successfully.']);

    }
}
