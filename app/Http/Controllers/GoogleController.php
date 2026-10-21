<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // "Login with Google" button eka click karaddi Google login page ekata yawanawa
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google eken login wela ayeth blog ekata enakota me method eka run wenawa
    public function callback()
    {
        // Local (XAMPP) fix: Guzzle ta CA file eka thiyenne kohedda kiyala kiyanawa
        // (nathnam XAMPP eke SSL certificate error ekak enawa)
        $googleUser = Socialite::driver('google')
            ->setHttpClient(new Client([
                'verify' => 'C:/xampp/php/extras/ssl/cacert.pem',
            ]))
            ->user();

        // Me Google email eka thiyena user kenek already blog eke inna nisa balanawa
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Already inna user nam, Google id eka witharak save karanawa
            $user->update(['google_id' => $googleUser->getId()]);
        } else {
            // Aluth user kenek nam aluthin account ekak hadanawa
            // Password eka random ekak (Google eken login wena nisa ona wenne na)
            // Aluth user la hama thanama "reader" role ekata yanawa
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
                'role' => 'reader',
            ]);
        }

        // User wa login karanawa ("remember me" ekath ekka)
        Auth::login($user, true);

        // Reader nam posts page ekata, admin nam dashboard ekata yawanawa
        if (strtolower($user->role ?? 'reader') === 'reader') {
            return redirect('/posts');
        }

        return redirect()->intended('/dashboard');
    }
}