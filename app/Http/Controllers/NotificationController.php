<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // NEW: Notifications page eka (All / Unread)
    public function index(Request $request)
    {
        $user   = auth()->user();
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $query = $filter === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query->latest()->paginate(15)->withQueryString();
        $unreadCount   = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    // Notification ek read ksrl e post ekt ynw
    public function read(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $data = $notification->data;
        $post = Post::find($data['post_id'] ?? null);

        if (! $post) {
            return redirect()->route('posts.index');
        }

        $url = route('posts.show', $post);

        if (($data['type'] ?? 'post') === 'comment') {
            $url .= '#comments';
        }

        return redirect()->to($url);
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}