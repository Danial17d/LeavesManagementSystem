<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\Structure;
use App\Models\User;
use App\Services\StructureServices;
use Couchbase\DesignDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use function Termwind\parse;


class StructureController extends Controller
{
    public function index()
    {
        Gate::authorize(PermissionType::StructureList);

        $root = Structure::where('manager_id', auth()->id())->first();

        $hierarchical = Structure::with(['manager:id,name','children','users:id,name,structure_id'])
            ->select('id','name','type','path' , 'parent_id', 'manager_id')
            ->where('path' , 'like' , $root->path ??" ".'%')
            ->get();


        return view('structures.index',[
            'hierarchical' => $hierarchical,
            'users' =>   User::select('id', 'name', 'email')->get()
        ]);
    }
    public function show(Structure $structure){

        Gate::authorize(PermissionType::StructureView);

        $employees = User::with('roles:name')
            ->select('id','name','email')
            ->where('structure_id' ,'=', $structure->id)
            ->paginate(10);


        return view('structures.show',[
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
            'name'       => ['required', Rule::unique('structures', 'name')],
            'type'       => ['required'],
            'manager_id' => ['required', Rule::unique('structures', 'manager_id'), 'exists:users,id'],
            'parent_id'  => ['sometimes', 'nullable', 'exists:structures,id'],
        ], [
            'manager_id.unique' => 'This user is already assigned as a manager for another structure.',
        ]);


        $structure = Structure::create($attributes);


        if (!empty($structure->parent_id)) {
            $parent = Structure::select('id', 'path')->findOrFail($structure->parent_id);

            $parentPath = $parent->path ?: (string) $parent->id;

            $structure->update([
                'path' => $parentPath . '.' . $structure->id,
            ]);
            } else {

            $structure->update([
                'path' => (string) $structure->id,
            ]);
        }

        return redirect()->back()->with('status', 'Structure created');
    }

}
