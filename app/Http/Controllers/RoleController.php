<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize(PermissionType::RoleList);

        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string'],
            'role_name' => ['nullable', 'string'],
            'sort_by' => Rule::in(['id','name','created_at']),
            'sort_dir' => Rule::in(['asc','desc']),
        ]);
        $roleName= mb_trim($request->string('role_name','')->toString());
        $search = mb_trim($request->string('search', '')->toString());

        $query = Role::query()
            ->when($roleName, function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            });;
        $sortBy  = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        $roles = $query->paginate($request->integer('per_page', 10));

        return view('roles.index', [
            'roles' => $roles,
            'roleName' => Role::query()->select('name')->orderBy('name')->get(),
        ]);
    }
    public function create()
    {
        Gate::authorize(PermissionType::RoleCreate);

        return view('roles.create', [
            'permissions' => PermissionType::cases(),
            'users' => User::select('name')->get()
        ]);
    }
    public function store(Request $request)
    {
        Gate::authorize(PermissionType::RoleCreate);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('roles.index')->with('status', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        Gate::authorize(PermissionType::RoleView);

        $role->load('permissions:id,name', 'users:id,name,email');

        return view('roles.show', [
            'role' => $role,
        ]);
    }

    public function edit(Role $role)
    {
        Gate::authorize(PermissionType::RoleEdit);

        $role->load('permissions:id,name');

        return view('roles.edit', [
            'role' => $role,
            'permissions' => PermissionType::cases(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize(PermissionType::RoleEdit);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('roles.index', $role)->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize(PermissionType::RoleDelete);

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Role deleted successfully.');
    }
}
