<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostPublished;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class PostController extends Controller
{
    // Cover image settings (WhatsApp / Facebook preview walata hodama size eka)
    private const COVER_MAX_WIDTH = 1200;
    private const COVER_JPEG_QUALITY = 78;

    // Home (PUBLIC): published posts witharai
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $posts = Post::with(['user', 'category'])
            ->withInteractions()
            ->where('status', 'published')
            ->when($search !== '', function ($query) use ($search) {
                // OR conditions tika group karala, status = published eka kaduenne nethi wenna
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->latest()
            ->paginate(6)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('posts.index', compact('posts', 'categories', 'search'));
    }

    // My Posts (published + pending) - thamange posts
    public function myPosts()
    {
        $posts = auth()->user()->posts()
            ->whereIn('status', ['published', 'pending'])
            ->with(['user', 'category'])
            ->withInteractions()
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('posts.my', compact('posts'));
    }

    // Drafts page
    public function drafts()
    {
        $posts = auth()->user()->posts()
            ->where('status', 'draft')
            ->with(['user', 'category'])
            ->withInteractions()
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('posts.drafts', compact('posts'));
    }

    // Admin: approval balagena inna posts
    public function pending()
    {
        abort_unless($this->isAdmin(), 403);

        $posts = Post::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(6);

        return view('posts.pending', compact('posts'));
    }

    // Admin: Approve karala publish karanawa, ethakota notification yanawa
    public function approve(Post $post)
    {
        abort_unless($this->isAdmin(), 403);
        abort_unless($post->status === 'pending', 404);

        $post->update(['status' => 'published']);

        $this->notifyUsers($post);

        return back()->with('success', 'Post approved and published.');
    }

    // Admin: Reject karala author ge Drafts ekata yawanawa
    public function reject(Post $post)
    {
        abort_unless($this->isAdmin(), 403);
        abort_unless($post->status === 'pending', 404);

        $post->update(['status' => 'draft']);

        return back()->with('success', 'Post sent back to the author as a draft.');
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->logUploadProblem($request);

        $data = $request->validate(array_merge([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:15360',
            'content'     => 'required|string',
            'status'      => 'required|in:draft,published',
        ], $this->metaRules()));

        // Admin nowana kenek publish kalot pending wenawa (server side, maga harinna baha)
        if (! $this->isAdmin() && $data['status'] === 'published') {
            $data['status'] = 'pending';
        }

        $data['content']     = Purifier::clean($data['content'], 'blog');
        $data['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
        $data['slug']        = $this->makeSlug($data['title']);   // CHANGED: unique slug (URL eke id wenuwata)
        $data['user_id']     = auth()->id();

        $post = Post::create($data);

        if ($post->status === 'published') {
            $this->notifyUsers($post);

            return redirect()->route('posts.index')->with('success', 'Post published successfully!');
        }

        if ($post->status === 'pending') {
            return redirect()->route('posts.my')->with('success', 'Post submitted! It will appear after admin approval.');
        }

        return redirect()->route('posts.drafts')->with('success', 'Draft saved!');
    }

    // Post eka kiyaweema (PUBLIC) - login nethi visitors tath wada
    public function show(Post $post)
    {
        $userId = auth()->id(); // visitor nam null

        // Draft / Pending balanna puluwan owner saha admin ta witharai
        abort_if(
            in_array($post->status, ['draft', 'pending']) && $post->user_id !== $userId && ! $this->isAdmin(),
            404
        );

        // Unique views: owner ge views ganan ganne naha, eka visitor ta paeyakata warak witharai
        if ($userId !== $post->user_id && $post->status === 'published') {
            $key = 'viewed_post_' . $post->id;

            if (now()->timestamp - session($key, 0) > 3600) {
                $post->increment('views');
                session([$key => now()->timestamp]);
            }
        }

        $post->load(['user', 'category']);
        $comments = $post->comments()->with('user')->latest()->get();
        $readTime = max(1, ceil(str_word_count(strip_tags($post->content)) / 200));

        $likesCount = $post->likedBy()->count();

        // Login nethi visitors ta liked / bookmarked = false
        $liked      = $userId ? $post->likedBy()->where('users.id', $userId)->exists() : false;
        $bookmarked = $userId ? $post->bookmarkedBy()->where('users.id', $userId)->exists() : false;

        return view('posts.show', compact('post', 'comments', 'readTime', 'likesCount', 'liked', 'bookmarked'));
    }

    public function edit(Post $post)
    {
        $this->authorizeOwner($post);
        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizeOwner($post);
        $this->logUploadProblem($request);

        $data = $request->validate(array_merge([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:15360',
            'content'     => 'required|string',
            'status'      => 'required|in:draft,published',
        ], $this->metaRules()));

        // Admin nowana kenek publish kalot (aluthin ho edit karala) ayeth pending wenawa
        if (! $this->isAdmin() && $data['status'] === 'published') {
            $data['status'] = 'pending';
        }

        $data['content'] = Purifier::clean($data['content'], 'blog');

        if ($request->hasFile('cover_image')) {
            // Parana image eka thiyenawa nam witharak delete karanawa
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $data['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
        } else {
            // Aluth file ekak nethnam parana image eka thiyagannawa
            unset($data['cover_image']);
        }

        $wasPublished = $post->status === 'published';

        $post->update($data);

        // Kalin published nowuna post ekak dan published wuna nam (admin) notification yanawa
        if (! $wasPublished && $post->status === 'published') {
            $this->notifyUsers($post);
        }

        $message = match ($post->status) {
            'draft'   => 'Draft updated!',
            'pending' => 'Post submitted! It will appear after admin approval.',
            default   => 'Post updated!',
        };

        return redirect()->route('posts.show', $post)->with('success', $message);
    }

    // Move to Trash (soft delete)
    public function destroy(Post $post)
    {
        $this->authorizeOwner($post);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post moved to trash.');
    }

    // Trash page - admin ta hama trashed post ekama penawa
    public function trash()
    {
        $posts = $this->trashedQuery()
            ->with('category')
            ->latest('deleted_at')
            ->paginate(6)
            ->withQueryString();

        return view('posts.trash', compact('posts'));
    }

    // CHANGED: $id wenuwata slug
    public function restore(string $slug)
    {
        $post = $this->trashedQuery()->where('slug', $slug)->firstOrFail();
        $post->restore();

        return redirect()->route('posts.trash')->with('success', 'Post restored!');
    }

    // Trash eken sadahatama delete karanna (CHANGED: $id wenuwata slug)
    public function forceDelete(string $slug)
    {
        $post = $this->trashedQuery()->where('slug', $slug)->firstOrFail();

        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->forceDelete();

        return redirect()->route('posts.trash')->with('success', 'Post permanently deleted.');
    }

    // NEW: Unique slug ekak hadanawa: title eken (max 60) + random 6.
    // Title eken slug ekak hadanna bari nam (ex: Sinhala witharai) "post" use karanawa.
    private function makeSlug(string $title): string
    {
        $base = Str::limit(Str::slug($title), 60, '') ?: 'post';

        do {
            $slug = $base . '-' . Str::lower(Str::random(6));
        } while (Post::withTrashed()->where('slug', $slug)->exists());

        return $slug;
    }

    // NEW: SEO meta fields validate karanne admin ta witharai.
    // Admin nowana kenek meta yawwath validate wenne naha, ethakota save wenneth naha
    // (reader kenek thamange post eka edit kalata, admin kalin dapu meta maka wenne naha).
    private function metaRules(): array
    {
        if (! $this->isAdmin()) {
            return [];
        }

        return [
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords'    => 'nullable|string|max:255',
        ];
    }

    // CHANGED: Post eka publish wunama anith hama user kenekta notification (bell + email)
    // Post eke author saha approve karapu admin ta yawanne naha
    private function notifyUsers(Post $post): void
    {
        $post->loadMissing('user');

        User::whereNotIn('id', [$post->user_id, auth()->id()])->get()
            ->each(function ($user) use ($post) {
                // Eka user kenekge email eka fail unath, publish kireema / ithuru ayata yana eka kaduenne naha
                try {
                    $user->notify(new NewPostPublished($post));
                } catch (\Throwable $e) {
                    Log::warning('Post notification failed', [
                        'user_id' => $user->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            });
    }

    // Admin ta ho post eke owner ta witharai, anith ayata 403
    private function authorizeOwner(Post $post): void
    {
        abort_if(! $this->isAdmin() && $post->user_id !== auth()->id(), 403);
    }

    // Trashed posts: admin ta siyalluma, anith ayata thamange ewa witharai
    private function trashedQuery()
    {
        $query = Post::onlyTrashed();

        if (! $this->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    // Admin check eka thiyenne mehema witharai
    // Oyage role eka wena widiyakata (ex: is_admin) thiyenawa nam meka witharak wenas karanna.
    private function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Cover image eka auto resize + compress karala save karanawa.
     * - width 1200px ta wada loku nam 1200px ta adu karanawa (kuda nam ehemama)
     * - JPEG quality 78 ekin save karanawa (samanyayen 300KB ta aduyi)
     * - PNG / WebP th JPEG walata harenawa (transparent thaen suddu wenawa)
     * - GD wada karanne nethnam / image eka kiyawanna bari nam original ekama save karanawa
     *
     * Return: public disk eke path eka (ex: posts/xxxx.jpg)
     */
    private function storeCoverImage(UploadedFile $file): string
    {
        try {
            $realPath = $file->getRealPath();
            $info     = $realPath ? @getimagesize($realPath) : false;

            if (! $info || ! function_exists('imagecreatetruecolor')) {
                return $file->store('posts', 'public');
            }

            $source = match ($info['mime']) {
                'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($realPath) : false,
                'image/png'  => function_exists('imagecreatefrompng') ? @imagecreatefrompng($realPath) : false,
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : false,
                default      => false,
            };

            if (! $source) {
                return $file->store('posts', 'public');
            }

            // Phone photos wala harawala pena eka niwaradi karnw (EXIF orientation)
            if ($info['mime'] === 'image/jpeg' && function_exists('exif_read_data')) {
                $exif = @exif_read_data($realPath);
                $orientation = $exif['Orientation'] ?? 1;
                $angle = match ($orientation) {
                    3 => 180,
                    6 => -90,
                    8 => 90,
                    default => 0,
                };
                if ($angle !== 0) {
                    $rotated = imagerotate($source, $angle, 0);
                    if ($rotated) {
                        imagedestroy($source);
                        $source = $rotated;
                    }
                }
            }

            $width  = imagesx($source);
            $height = imagesy($source);

            $newWidth  = min($width, self::COVER_MAX_WIDTH);
            $newHeight = (int) round($height * ($newWidth / $width));

            $canvas = imagecreatetruecolor($newWidth, $newHeight);
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            ob_start();
            imagejpeg($canvas, null, self::COVER_JPEG_QUALITY);
            $jpeg = ob_get_clean();

            imagedestroy($source);
            imagedestroy($canvas);

            $path = 'posts/' . Str::random(40) . '.jpg';
            Storage::disk('public')->put($path, $jpeg);

            return $path;
        } catch (\Throwable $e) {
            Log::warning('Cover image resize failed, saving original', [
                'message' => $e->getMessage(),
            ]);

            return $file->store('posts', 'public');
        }
    }

    // Upload fail unoth haraiytm hethuw storage/logs/laravel.log ekat liynw
    private function logUploadProblem(Request $request): void
    {
        $file = $request->file('cover_image');

        if ($file && ! $file->isValid()) {
            Log::warning('Cover image upload failed', [
                'error_code'    => $file->getError(),
                'error_message' => $file->getErrorMessage(),
                'size'          => $file->getSize(),
            ]);
        }
    }
}