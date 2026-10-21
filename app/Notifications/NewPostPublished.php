<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewPostPublished extends Notification
{
    public function __construct(public Post $post)
    {
    }

    // CHANGED: bell (database) + email
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // NEW: Email eka
    public function toMail(object $notifiable): MailMessage
    {
        $excerpt = Str::limit(
            trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($this->post->content)))),
            160
        );

        return (new MailMessage)
            ->subject('New post: ' . $this->post->title)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line($this->post->user->name . ' just published a new post on MyBlog:')
            ->line('**' . $this->post->title . '**')
            ->line($excerpt)
            ->action('Read post', route('posts.show', $this->post))
            ->salutation('MyBlog');
    }

    // Bell notification eke data (wenas karala na)
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title'   => $this->post->title,
            'author'  => $this->post->user->name,
        ];
    }
}