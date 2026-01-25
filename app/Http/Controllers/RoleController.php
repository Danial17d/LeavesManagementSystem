<?php

namespace App\Http\Controllers;

use App\Enum\PermissionType;
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
            'sort' => ['nullable', 'array'],
            'sort.*.field' => [Rule::in(['id', 'name', 'created_at'])],
            'sort.*.direction' => [Rule::in(['asc', 'desc'])],
        ]);
        $search = mb_trim($request->string('search', '')->toString());

        $query = Role::query()->when($search, function ($query) use ($search) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        });
        $sorts = collect($validated['sort'] ?? [['field' => 'id', 'direction' => 'desc']]);
        $sorts->each(fn (array $sort) => $query->orderBy($sort['field'], $sort['direction']));

        $roles = $query->paginate($request->integer('per_page', 10));
        return view('roles.index', [
            'roles' => $roles,

        ]);
    }
    public function create(){

    }
    public function store(Request $request){

        $validated = $request->validate([
            'name' => 'required',
        ]);
        $role = Role::find($validated['name']);

        if(! $role){
            Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);
        }
        return redirect('/roles');
    }
}
