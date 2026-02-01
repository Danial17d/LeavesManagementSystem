<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['type','days','steps'];

    public function leavesRequest(){
        return $this->hasMany(LeaveRequest::Class);
    }
}
