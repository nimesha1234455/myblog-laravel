<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class FollowController extends Controller
{
    // Follow / Unfollow toggle
    public function toggle(User $user)
    {
        $me = auth()->user();

        if ($me->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        $me->following()->toggle($user->id);

        return back();
    }

    // Posts from people I follow
    public function feed()
    {
        $followingIds = auth()->user()->following()->pluck('users.id');

        $posts = Post::with(['user', 'category'])
            ->withInteractions()
            ->whereIn('user_id', $followingIds)
            ->where('status', 'published')
            ->latest()
            ->paginate(9);

        // Suggested writers (shown when the feed is empty)
        $suggested = User::where('id', '!=', auth()->id())
            ->whereNotIn('id', $followingIds)
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->take(5)
            ->get();

        return view('following', compact('posts', 'suggested'));
    }
}