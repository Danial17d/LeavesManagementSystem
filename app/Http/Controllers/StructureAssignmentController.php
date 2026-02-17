<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class StructureAssignmentController extends Controller
{

    public function store(Request $request)
    {

        Gate::authorize(PermissionType::StructureAssign);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'structure_id' => ['required', 'integer', 'exists:structures,id'],
        ]);

        $employee = User::find($validated['user_id']);

        $update = $employee->update([
            'structure_id' => $validated['structure_id'],
        ]);

        if(! $update){
            return back()->withErrors(['error' => 'something went wrong']);
        }
        return redirect()->back()->with('status', 'User assigned successfully.');
    }
    public function edit(Structure $structure)
    {
        Gate::authorize(PermissionType::StructureMove);

        $employees = User::with('roles:name')
            ->select('id', 'name', 'email', 'structure_id')
            ->where('structure_id', $structure->id)
            ->orderBy('name')
            ->paginate(10);

        $targetStructures = Structure::query()
            ->select('id', 'name', 'type')
            ->where('id', '!=', $structure->id)
            ->orderBy('name')
            ->get();

        return view('structure-assignment.edit', [
            'structure' => $structure,
            'employees' => $employees,
            'targetStructures' => $targetStructures,
        ]);

    }
    public function update(Request $request)
    {

        Gate::authorize(PermissionType::StructureMove);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'from_structure_id' => ['required', 'integer', 'exists:structures,id'],
            'to_structure_id' => ['required', 'integer', 'exists:structures,id', 'different:from_structure_id'],
        ]);

        $employee = User::query()
            ->select('id', 'name', 'structure_id')
            ->where('id', $validated['user_id'])
            ->where('structure_id', $validated['from_structure_id'])
            ->first();

        if (! $employee) {
            return back()->withErrors(['error' => 'User was not found in the selected source structure.']);
        }

        $employee->update([
            'structure_id' => $validated['to_structure_id'],
        ]);

        return redirect()
            ->route('structures.index', ['structure' => $validated['from_structure_id']])
            ->with('status', 'User moved successfully.');

    }
}
