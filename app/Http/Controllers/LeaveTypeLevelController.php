<?php

namespace App\Http\Controllers;

use App\Models\LeaveTypeApprovalRule;
use Illuminate\Http\Request;

class LeaveTypeLevelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'levels' => ['required', 'array'],
            'levels.*' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->levels as $leaveTypeId => $level) {
            LeaveTypeApprovalRule::updateOrCreate(
                ['leave_type_id' => $leaveTypeId],
                ['level' => $level]
            );
        }

        return redirect()->route('leave-types.index')->with('status', 'Levels updated successfully.');
    }
}