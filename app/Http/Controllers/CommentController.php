<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentOnPost;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        // Published posts witharai  comment karanna puluwan
        abort_unless($post->status === 'published', 404);

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);

        // post eke aithi karayat notification ekk (thamangem postwalt comment karaotha na )
        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new NewCommentOnPost($post, $comment));
        }

        return redirect()->to(route('posts.show', $post) . '#comments')
            ->with('success', 'Comment added!');
    }

    public function destroy(Comment $comment)
    {
        $post = $comment->post;
        $user = auth()->user();

        // Comment ek liypu kenat, post post eke aithi karayt, nathtnm admin admint witgharai
        $allowed = $comment->user_id === $user->id
            || $post->user_id === $user->id
            || $user->role === 'admin';

        abort_unless($allowed, 403);

        $comment->delete();

        return redirect()->to(route('posts.show', $post) . '#comments')
            ->with('success', 'Comment deleted.');
    }
}