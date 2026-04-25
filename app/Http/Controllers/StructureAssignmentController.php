<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;


class StructureAssignmentController extends Controller
{
    public function create(Structure $structure)
    {
        Gate::authorize(PermissionType::StructureAssign);

        return view('structure_assignment.create', [
            'structure' => $structure,
            'users' => User::select('users.uuid', 'users.name')
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'employee')
                ->where('users.id', '!=', auth()->id())
                ->whereNull('users.structure_id')
                ->orderBy('users.name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {

        Gate::authorize(PermissionType::StructureAssign);

        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'uuid', 'exists:users,uuid', 'distinct'],
            'structure_id' => ['required', 'integer', 'exists:structures,id'],
        ]);

        $employees = User::query()
            ->select('id')
            ->whereIn('uuid', $validated['user_ids'])
            ->whereNull('structure_id')
            ->get();

        if ($employees->count() !== count($validated['user_ids'])) {
            return back()
                ->withInput()
                ->withErrors(['user_ids' => 'One or more selected users are already assigned to a structure.']);
        }

        $userIds = $employees->pluck('id')->all();

        $updatedCount = DB::table('users')
            ->whereIn('id', $userIds)
            ->update([
                'structure_id' => $validated['structure_id'],
            ]);

        if ($updatedCount !== count($userIds)) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while assigning the selected users.']);
        }

        return redirect()
            ->route('structures.show', $validated['structure_id'])
            ->with('status', $updatedCount.' user(s) assigned successfully.');
    }
    public function edit(Structure $structure)
    {
        Gate::authorize(PermissionType::StructureMove);

        $employees = User::with('roles:name')
            ->select('id', 'uuid', 'name', 'email', 'structure_id')
            ->where('structure_id', $structure->id)
            ->orderBy('name')
            ->paginate(10);

        $targetStructures = Structure::query()
            ->select('id', 'name', 'type')
            ->where('id', '!=', $structure->id)
            ->orderBy('name')
            ->get();

        return view('structure_assignment.edit', [
            'structure' => $structure,
            'employees' => $employees,
            'targetStructures' => $targetStructures,
        ]);

    }
    public function update(Request $request)
    {

        Gate::authorize(PermissionType::StructureMove);

        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,uuid'],
            'from_structure_id' => ['required', 'integer', 'exists:structures,id'],
            'to_structure_id' => ['required', 'integer', 'exists:structures,id', 'different:from_structure_id'],
        ]);

        $employee = User::query()
            ->select('id', 'name', 'structure_id')
            ->where('uuid', $validated['user_id'])
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
