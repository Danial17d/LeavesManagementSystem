<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayRoll extends Model
{
    protected $table = 'payroll_records';

    protected $fillable = [
        'user_id',
        'basic_salary',
        'net_salary',
        'unpaid_deduction',
        'year',
        'month',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
