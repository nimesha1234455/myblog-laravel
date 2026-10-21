<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostInteractionController extends Controller
{
    public function like(Post $post)
    {
        $result = $post->likedBy()->toggle(auth()->id());

        return back()->with(
            'success',
            count($result['attached']) ? 'You liked this post.' : 'Like removed.'
        );
    }

    public function bookmark(Post $post)
    {
        $result = $post->bookmarkedBy()->toggle(auth()->id());

        return back()->with(
            'success',
            count($result['attached']) ? 'Post saved to your Saved Posts.' : 'Removed from Saved Posts.'
        );
    }

    // Saved Posts page
    public function saved()
    {
        $posts = Post::whereHas('bookmarkedBy', fn ($q) => $q->where('users.id', auth()->id()))
            ->with(['user', 'category'])
            ->withInteractions()
            ->latest()
            ->paginate(6);

        return view('posts.saved', compact('posts'));
    }
}