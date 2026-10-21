<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(Request $request)
    {
        $request->validateWithBag('avatar', [
            'avatar' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        $user = $request->user();

        // parana image ek store eken ain karanw
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = $request->file('avatar')->store('avatars', 'public');
        $user->save();

        return back()->with('status', 'avatar-updated');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return back()->with('status', 'avatar-removed');
    }
}