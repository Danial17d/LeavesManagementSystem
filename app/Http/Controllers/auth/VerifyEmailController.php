<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function create()
    {
        return view('auth.verify-email');
    }

    public function store(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent.');
    }

    public function update(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('dashboard')->with('status', 'Email verified successfully.');
        }

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')->with('status', 'Email verified successfully. Please sign in.');
    }
}
