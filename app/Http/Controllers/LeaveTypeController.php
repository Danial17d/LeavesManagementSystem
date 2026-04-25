<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize(PermissionType::LeaveTypeList);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['id', 'name', 'days', 'level'])],
            'dir'  => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $leaveTypes = LeaveType::query()
            ->with('approvalRule')
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($validated['sort'] ?? 'id', $validated['dir'] ?? 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('leave_types.index', [
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function create()
    {
        Gate::authorize(PermissionType::LeaveTypeCreate);

        return view('leave_types.create');
    }

    public function store(Request $request)
    {
        Gate::authorize(PermissionType::LeaveTypeCreate);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name'],
            'days' => ['nullable', 'integer', 'min:1'],
            'level' => ['required', 'integer', 'min:1'],
        ]);

        $leaveType = LeaveType::create([
            'name' => $validated['name'],
            'days' => $validated['days'],
        ]);

        $leaveType->approvalRule()->create([
            'level' => $validated['level'],
        ]);

        return redirect()->route('leave-types.index')->with('status', 'Leave type created successfully.');
    }

    public function show(LeaveType $leaveType)
    {
        Gate::authorize(PermissionType::LeaveTypeView);

        return view('leave_types.show', [
            'leaveType' => $leaveType,
        ]);
    }

    public function edit(LeaveType $leaveType)
    {
        Gate::authorize(PermissionType::LeaveTypeEdit);

        return view('leave_types.edit', [
            'leaveType' => $leaveType,
        ]);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        Gate::authorize(PermissionType::LeaveTypeEdit);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('leave_types', 'name')->ignore($leaveType->id)],
            'days' => ['nullable', 'integer', 'min:1'],
            'level' => ['required', 'integer', 'min:1'],
        ]);

        $leaveType->update([
            'name' => $validated['name'],
            'days' => $validated['days'],
        ]);

        $leaveType->approvalRule()->updateOrCreate(
            ['leave_type_id' => $leaveType->id],
            ['level' => $validated['level']]
        );

        return redirect()->route('leave-types.index', $leaveType)->with('status', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        Gate::authorize(PermissionType::LeaveTypeDelete);

        $leaveType->delete();

        return redirect()->route('leave-types.index')->with('status', 'Leave type deleted successfully.');
    }
}
