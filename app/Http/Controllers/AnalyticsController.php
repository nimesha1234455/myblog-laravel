<?php

namespace App\Http\Controllers;

use App\Models\Category;

class AnalyticsController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        $posts = $user->posts()
            ->where('status', 'published')
            ->with('category')
            ->withCount(['comments', 'likedBy', 'bookmarkedBy'])
            ->get();

        $totals = [
            'views'    => (int) $posts->sum('views'),
            'likes'    => (int) $posts->sum('liked_by_count'),
            'comments' => (int) $posts->sum('comments_count'),
            'saves'    => (int) $posts->sum('bookmarked_by_count'),
        ];

        // Top 5 posts by views
        $topPosts = $posts->sortByDesc('views')->take(5)->values();

        // Category breakdown
        $categories = $posts->groupBy('category_id')->map(function ($group) {
            return [
                'name'  => $group->first()->category->name,
                'posts' => $group->count(),
                'views' => (int) $group->sum('views'),
            ];
        })->sortByDesc('views')->values();

        return view('analytics', compact('totals', 'topPosts', 'categories'));
    }
}