<?php

namespace App\Http\Controllers;


use App\Enums\PermissionType;
use App\Http\Requests\LeaveRequestStore;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LeaveRequestController extends Controller
{
    public function index()
    {
        return view('leave_requests.index');
    }
    public function create()
    {
        $leaveRequest = LeaveRequest::with('user')->where('user_id', auth()->id())->first();


        return view('leave_requests.create',[
            'leaveRequest' => $leaveRequest,
            'leaveTypes' =>LeaveType::with('approvalRule')->select('name','days')->get()
        ]);
    }
    public function store(LeaveRequestStore $request){

        Gate::authorize(PermissionType::LeaveRequestCreate);

        $request->validate([]);

        $leaveTypeId = LeaveType::where('name',$request->leave_type)->first()->id;

        if($request->hasFile('attachment')){

            $request->file('attachment')->store('attachments');
        }


        $leaveRequest = LeaveRequest::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $leaveTypeId,
            'from' => $request->start_date,
            'to' => $request->end_date,
            'reason' => $request->reason,
            'attachment' => $request->attachment,
        ]);



    }
}
