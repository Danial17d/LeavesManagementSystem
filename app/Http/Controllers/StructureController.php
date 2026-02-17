<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;


class StructureController extends Controller
{
    public function index()
    {
        Gate::authorize(PermissionType::StructureList);

        $hierarchical = Structure::query()
            ->with(['manager:id,name'])
            ->withCount('users')
            ->where('manager_id', auth()->id())
            ->first();

        $nodesByParent = collect();

        if ($hierarchical) {
            $nodes = $hierarchical->descendantsAndSelf()
                ->with(['manager:id,name'])
                ->withCount('users')
                ->get(['id', 'name', 'type', 'parent_id', 'manager_id']);

            $nodesByParent = $nodes->groupBy('parent_id');
        }

        return view('structures.index',[
            'hierarchical' => $hierarchical,
            'nodesByParent' => $nodesByParent,
            'users' =>   User::select('id', 'name', 'email')->get()
        ]);
    }
    public function show(Structure $structure,Request $request){

        Gate::authorize(PermissionType::StructureView);

        $validated = $request->validate([
            'search' =>['nullable','string'],
        ]);

        $search = trim((string) $request->input('search'));

        $employees = User::with('roles:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->where('structure_id', $structure->id)
            ->select('id', 'name', 'email')
            ->paginate(5);


        return view('structures.show',[
            'structure' => $structure,
            'employees' => $employees,
            'users' =>User::select('id', 'name')
                ->where('id', '!=', auth()->id())
                ->whereNull('structure_id')
                ->get()
        ]);
    }
    public function store(Request $request)
    {
        Gate::authorize(PermissionType::StructureCreate);

        $attributes = $request->validate([
            'name' => ['required', Rule::unique('structures', 'name')],
            'type' => ['required'],
            'manager_id' => ['required', Rule::unique('structures', 'manager_id'), 'exists:users,id'],
            'parent_id' => ['sometimes', 'nullable', 'exists:structures,id'],
        ], [
            'manager_id.unique' => 'This user is already assigned as a manager for another structure.',
        ]);

         Structure::create($attributes);

        return redirect()->back()->with('status', 'Structure created');
    }
    public function destroy(Structure $structure){

        Gate::authorize(PermissionType::StructureDelete);

        User::where('structure_id' ,$structure->id)->update(['structure_id' => null]);

        foreach ($structure->descendantsAndSelf as $descendant) {

            $descendant->delete();
        }

        return redirect()->route('structures.index')->with('status', 'Structure deleted');

    }

}
