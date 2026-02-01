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
            'roleName' => Role::all()

        ]);
    }
    public function create(){

        return view('roles.create', [
            'permissions' => PermissionType::cases(),
            'users' => User::query()
                ->select( 'name')
                ->doesntHave('roles')
                ->get(),
        ]);


    }
    public function store(Request $request){

        $validated = $request->validate([
            'name' => 'required',
            'permissions' => ['required', 'array'],
            'user_name' => ['required', 'integer', 'exists:users,name'],
        ]);
        $role = Role::find($validated['name']);
        $user = User::find([$validated['user_name']]);

        if(! $role){
            Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);
        }
        $role->givePermissionTo($validated['permissions']);

        $user->assignRole($role);

        return redirect('/roles');
    }
}
