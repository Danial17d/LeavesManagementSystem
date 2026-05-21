<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\ApprovalRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $setupChecklist = null;
        $adminStats  = null;
        $employeeStats  = null;

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            $steps = [
                [
                    'done'  => User::whereHas('roles', fn($q) => $q->where('name', UserRole::Admin->value))->exists(),
                    'label' => 'Assign at least one admin role',
                    'hint'  => 'Assign the Admin role to at least one user so they can manage structures and employees.',
                    'route' => route('users.index'),
                ],
                [
                    'done'  => Structure::exists(),
                    'label' => 'Set up the organisation structure',
                    'hint'  => 'Define your departments and hierarchy before adding employees.',
                    'route' => route('structures.create'),
                ],
                [
                    'done'  => LeaveType::exists() && LeaveType::all()->every(fn($lt) => $lt->approvalRule?->level !== null),
                    'label' => 'Set an approval level for every leave type',
                    'hint'  => 'Each leave type needs an approval depth so requests can flow through the hierarchy.',
                    'route' => route('leave-types.index'),
                ],
            ];

            $setupChecklist = [
                'allDone' => collect($steps)->every(fn($s) => $s['done']),
                'steps'   => $steps,
            ];
        }


        if ($user->hasRole(UserRole::SuperAdmin->value) || $user->hasRole(UserRole::Admin->value)) {
            $isSuperAdmin = $user->hasRole(UserRole::SuperAdmin->value);

            $user->loadMissing('managedStructure.parent', 'managedStructure.users');

            $adminStats = [
                'totalEmployees'   => User::whereHas('roles', fn($q) => $q->where('name', UserRole::Employee->value))->count(),
                'managedStructure' => $user->managedStructure,

                'pendingApprovals' => ApprovalRequest::where('status', RequestStatus::Pending->value)
                    ->when(!$isSuperAdmin, fn($q) => $q->where('approver_id', $user->id))
                    ->count(),

                'approvedThisMonth' => LeaveRequest::where('status', RequestStatus::Approved->value)
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->count(),

                'totalLeaveTypes' => LeaveType::count(),

                'recentRequests' => LeaveRequest::with(['user', 'leaveType'])
                    ->latest()
                    ->take(6)
                    ->get(),

                'byStatus' => [
                    RequestStatus::Submitted->value => LeaveRequest::where('status', RequestStatus::Submitted->value)->count(),
                    RequestStatus::Pending->value  => LeaveRequest::where('status', RequestStatus::Pending->value)->count(),
                    RequestStatus::Approved->value => LeaveRequest::where('status', RequestStatus::Approved->value)->count(),
                    RequestStatus::Rejected->value  => LeaveRequest::where('status', RequestStatus::Rejected->value)->count(),
                    RequestStatus::Cancelled->value => LeaveRequest::where('status', RequestStatus::Cancelled->value)->count(),
                ],

                'totalRequests' => LeaveRequest::count(),
            ];
        }

        if ($user->hasRole(UserRole::Employee->value)) {
            $year = now()->year;

            $user->loadMissing('structure.manager', 'structure.parent');

            $employeeStats = [
                'structure'     => $user->structure,
                'leaveBalances' => $user->leaveBalances()
                    ->with('leaveType')
                    ->where('year', $year)
                    ->get(),

                'recentRequests' => $user->leave()
                    ->with('leaveType')
                    ->latest()
                    ->take(5)
                    ->get(),

                'pendingCount' => $user->leave()
                    ->whereIn('status', [RequestStatus::Submitted->value, RequestStatus::Pending->value])
                    ->count(),

                'approvedDaysThisYear' => (int) $user->leave()
                    ->where('status', RequestStatus::Approved->value)
                    ->whereYear('created_at', $year)
                    ->sum('requested_days'),

                'totalRequestsThisYear' => $user->leave()
                    ->whereYear('created_at', $year)
                    ->count(),

                'payrolls' => $user->payrolls()
                    ->where('year', $year)
                    ->orderBy('month', 'desc')
                    ->get(),
            ];
        }

        return view('dashboard', compact('setupChecklist', 'adminStats', 'employeeStats'));
    }
}
