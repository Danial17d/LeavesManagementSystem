<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {

    }
    public function create(){

    }
    public function store(Request $request){

        $validated = $request->validate([
            'name' => 'required',
        ]);
        $role = Role::find($validated['name']);

        if(! $role){
            Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);
        }
        return redirect('/roles');
    }
}
