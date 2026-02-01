<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * List users with search + role filter + sorting + pagination
     */
    public function index(Request $request)
    {
        Gate::authorize(PermissionType::UserList);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:80'],
            'sort' => ['nullable', Rule::in(['name', 'email', 'created_at'])],
            'dir'  => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $users = User::query()
            ->with('roles:id,name')
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(fn ($qq) =>
                $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                );
            })
            ->when($validated['role'] ?? null, function ($query, $role) {
                $query->whereHas('roles', fn ($r) => $r->where('name', $role));
            })
            ->orderBy($validated['sort'] ?? 'created_at', $validated['dir'] ?? 'desc')
            ->paginate( 10)
            ->withQueryString();


            $roles = Role::select('id', 'name')->orderBy('name')->get();

            return view('users.index', [
                'users' => $users,
                'roles' => $roles,
            ]);

    }

    public function create()
    {
        Gate::authorize(PermissionType::UserCreate);

        $roles = Role::select('id', 'name')->orderBy('name')->get();
        return view('users.create',[
            'roles' => $roles
            ]);
    }

    public function store(Request $request)
    {
        Gate::authorize(PermissionType::UserCreate);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return $request->wantsJson()
            ? response()->json($user->load('roles:id,name'), 201)
            : redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function show(User $user, Request $request)
    {
        Gate::authorize(PermissionType::UserView);

        $user->load('roles:id,name');

        return $request->wantsJson()
            ? response()->json($user)
            : view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        Gate::authorize(PermissionType::UserEdit);

        $user->load('roles:id,name');
        $roles = Role::select('id', 'name')->orderBy('name')->get();

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize(PermissionType::UserEdit);

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles'  => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        if (isset($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->back()->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        Gate::authorize(PermissionType::UserDelete);

        if ($request->user()->id === $user->id) {
            return back()->withErrors(['delete' => "You can't delete your own account."]);
        }

        $user->delete();

        return redirect()->back()->with('status', 'User deleted successfully.');
    }
}

