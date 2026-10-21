<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Post;

class PostObserver
{
    public function created(Post $post): void
    {
        ActivityLog::record('post.created', 'Created post "' . $post->title . '"', $post);
    }

    public function updated(Post $post): void
    {
        if ($post->wasChanged('status')) {
            ActivityLog::record(
                'post.status',
                'Post "' . $post->title . '" status changed to ' . $post->status,
                $post
            );
            return;
        }

        ActivityLog::record('post.updated', 'Updated post "' . $post->title . '"', $post);
    }

    public function deleted(Post $post): void
    {
        ActivityLog::record('post.deleted', 'Deleted post "' . $post->title . '"', $post);
    }

    public function restored(Post $post): void
    {
        ActivityLog::record('post.restored', 'Restored post "' . $post->title . '"', $post);
    }
}