<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->get('token'),
            'email' => $request->get('email'),
        ]);
    }
    public function store(Request $request)
    {

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $token = $request->token;
        $email = $request->email;

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();



        if ($token !== $resetToken->token) {
            return back()->withErrors([
                'token-invalid' => 'Invalid reset token',
            ]);
        }


        if (Carbon::parse($resetToken->created_at)->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            return back()->withErrors([
                'token-expiration' => 'Reset token has expired',
            ]);
        }


        User::where('email', $email)->update([
            'password' => Hash::make($request->password),
        ]);


        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();


        return redirect('/login')
            ->with('status', 'Password reset successfully');
    }
}
