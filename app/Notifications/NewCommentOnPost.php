<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Notifications\Notification;

class NewCommentOnPost extends Notification
{
    public function __construct(public Post $post, public Comment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'comment',
            'post_id' => $this->post->id,
            'title'   => $this->post->title,
            'author'  => $this->comment->user->name,
        ];
    }
}