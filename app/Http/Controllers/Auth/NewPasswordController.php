<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    // Password reset karana page eka pennanawa (email eke link eka click karaddi me page eka open wenawa)
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    // Aluth password eka save karanawa
    //
    // @throws ValidationException
    public function store(Request $request): RedirectResponse
    {
        // Token eka, email eka, password eka (confirm karala) hari kiyala balanawa
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Dan user ge password eka reset karanna try karanawa.
        // Token eka hari nam user ge password eka wenas karala database ekata save karanawa.
        // Hari nathnam error eka gannawa.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Password eka hariyata reset una nam login page ekata yawanawa.
        // Error ekak unoth kalin inna page ekatama yawala error message eka pennanawa.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}