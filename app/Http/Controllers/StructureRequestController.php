<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\Structure;
use App\Models\StructureRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StructureRequestController extends Controller
{
    public function index()
    {
        Gate::authorize(PermissionType::StructureRequestList);

        $structureRequests = StructureRequest::with(['structure', 'user', 'approval.approver'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(5);

        return view('structure_requests.index', [
            'structureRequests' => $structureRequests,
        ]);
    }

    public function show(StructureRequest $structureRequest)
    {
       Gate::authorize(PermissionType::StructureRequestView);

        $structureRequest->load(['structure', 'user', 'approval.approver']);

        return view('structure_requests.show', compact('structureRequest'));
    }

    public function create()
    {
        $user = auth()->user()->loadMissing('structure');

        $structures = Structure::whereNot('id', $user->structure_id)->orderBy('name')->get();

        $structure = Structure::with('users')
            ->where('manager_id', auth()->id())
            ->whereHas('manager', function ($query) {
                $query->whereHas('roles', function ($rolesQuery) {
                    $rolesQuery->where('name', UserRole::Admin);
                });
            })
            ->first();

        $isAssign = $user->structure()->exists();


        return view('structure_requests.create', [
            'structures' => $structures,
            'structure' => $structure,
            'isAssign' => $isAssign,
            'currentStructure' => $user->structure,

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'structure_id' => ['required', 'exists:structures,id'],
            'type' =>['required', Rule::in(['assign','move'])],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $hasPending = StructureRequest::where('user_id', auth()->id())
            ->whereNotIn('status', [
                RequestStatus::Approved->value,
                RequestStatus::Rejected->value,
                RequestStatus::Cancelled->value,
            ])
            ->exists();

        if ($hasPending) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['structure_id' => 'You already have a pending structure transfer request.']);
        }

        StructureRequest::create([
            'user_id' => auth()->id(),
            'structure_id' => $request->structure_id,
            'type' => $request->type,
            'reason' => $request->reason,
            'status'  => RequestStatus::Submitted->value,
        ]);

        return redirect()->route('structure-requests.index')
            ->with('status', 'Structure transfer request submitted successfully.');
    }
}
