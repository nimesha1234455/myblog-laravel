<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    // "Forgot password" page eka pennanawa (email eka type karana page eka)
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    // Password reset link eka email ekata yawanawa
    //
    // @throws ValidationException
    public function store(Request $request): RedirectResponse
    {
        // Email eka hari widiyata type karalada kiyala balanawa
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Me user ta password reset link eka yawanna try karanawa.
        // Yawala iwara unama enna response eka balala, user ta pennanna ona message eka theruwa gannawa.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Link eka yawuna nam kalin inna page ekatama yawala "link eka yawwa" kiyala message eka pennanawa.
        // Error ekak unoth (ex: me email ekata user kenek nathnam) error message eka pennanawa.
        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}