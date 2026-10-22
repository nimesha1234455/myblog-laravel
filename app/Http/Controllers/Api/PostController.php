<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Posts list eka: published posts witharai, 10 k page ekakata, ?category=ID filter eka
    public function index(Request $request)
    {
        $posts = Post::query()
            ->where('status', 'published')
            ->when($request->query('category'), fn ($q, $id) => $q->where('category_id', $id))
            ->with(['user', 'category'])
            ->withCount(['comments', 'likedBy'])
            ->latest()
            ->paginate(10);

        // Hama post ekakama api hadapu format ekata maru karanawa (content eka list eke danne na)
        $posts->through(fn (Post $post) => $this->format($post));

        return response()->json($posts);
    }

    // Post ekak slug eken (/api/posts/my-post-a8k2xq). Published nathnam 404
    public function show(string $slug)
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['user', 'category'])
            ->withCount(['comments', 'likedBy'])
            ->firstOrFail();

        return response()->json($this->format($post, true));
    }

    // Post eka API response ekata hadana thana ($full = true nam content eka ekka)
    private function format(Post $post, bool $full = false): array
    {
        $data = [
            'id'              => $post->id,
            'title'           => $post->title,
            'slug'            => $post->slug,
            'excerpt'         => Str::limit(strip_tags($post->content), 150),
            'cover_image_url' => $this->fileUrl($post->cover_image),
            'category'        => $post->category?->only(['id', 'name']),
            'author'          => [
                'id'         => $post->user?->id,
                'name'       => $post->user?->name,
                'avatar_url' => $this->fileUrl($post->user?->avatar),
            ],
            'comments_count'  => $post->comments_count,
            'likes_count'     => $post->liked_by_count,
            'created_at'      => $post->created_at,
        ];

        if ($full) {
            $data['content'] = $post->content;
            $data['meta_description'] = $post->meta_description;
        }

        return $data;
    }

    // Image path eka full URL ekak karanawa (storage/ folder eken)
    private function fileUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        // Google avatar wage dewal dan full URL nam eka ehema thiyanawa
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}