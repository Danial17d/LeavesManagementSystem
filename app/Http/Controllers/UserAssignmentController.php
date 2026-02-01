<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Support\Facades\Gate;


class UserAssignmentController extends Controller
{

    public function store()
    {

        Gate::authorize(PermissionType::StructureAssign);

        request()->validate([
            'user_name' => 'required',
            'structure_id' => 'required',
        ]);

        $employee = User::select('id','name')->where('name','=',request('user_name'))->first();

        $update = $employee->update([
            'structure_id' => request('structure_id'),
        ]);

        if(! $update){
            return back()->withErrors(['error' => 'something went wrong']);
        }
        return redirect()->back()->with(['success' => 'User assigned successfully.']);
    }
    public function update(){

        Gate::authorize(PermissionType::StructureMove);

        request()->validate([
            'user_name' => 'required',
            'structure_id' => 'required'
        ]);


    }
}
