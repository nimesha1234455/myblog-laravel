<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Post;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        // Dashboard ek admin (blog owner) ta witharai. Readers Home ekt ywnw.
        if ($user->role !== 'admin') {
            return redirect()->route('posts.index');
        }

        $stats = [
            'total'     => $user->posts()->count(),
            'published' => $user->posts()->where('status', 'published')->count(),
            'drafts'    => $user->posts()->where('status', 'draft')->count(),
            'views'     => (int) $user->posts()->sum('views'),
        ];

        $latest = $user->posts()
            ->with(['user', 'category'])
            ->withInteractions()
            ->latest()
            ->take(4)
            ->get();

        $trending = Post::with('category')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->take(5)
            ->get();

        // Recent Activity: activity_logs table eken (site eke hamotama)
        $activities = ActivityLog::with('user')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'latest', 'trending', 'activities'));
    }
}