<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Services\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function create(){
        return view('auth.email');
    }
    public function store(Request $request,Url $url){
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],

        ]);
        $token = Hash::make(bin2hex(random_bytes(16)));
        $email = $request->string('email')->toString();


        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
        ]);
        Mail::to($email)->queue(
            new ResetPassword($url->generate('reset.password',['token' => $token, 'email' => $email]))
        );
        return redirect()->back()->with('status', 'We have sent a link to reset your password.');


    }
}
