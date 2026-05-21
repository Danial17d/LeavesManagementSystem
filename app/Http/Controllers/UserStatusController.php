<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Services\PayRollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserStatusController extends Controller
{
    public function __invoke(Request $request,PayRollService $payRollService){

        Gate::authorize(PermissionType::PayRollCalculate);

        auth()->user()->update([
            'is_return' => true
        ]);

        $payRollService->calculateSalaryForAbsentDays(auth()->user());

        return redirect()->route('dashboard')->with(['status' => 'New Salary Calculated']);

    }
}
