<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'cover_image',
        'content',
        'status',   // draft | pending | published
        'meta_description',   // NEW: SEO description (optional)
        'meta_keywords',      // NEW: SEO keywords (optional)
    ];

    // NEW: URL වල id වෙනුවට slug එක පාවිච්චි කරනවා (/posts/my-post-a8k2xq)
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Like karapu users siyalla labaganna relationship ek
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    // Save (bookmark) karapu users labaganna realationship ek
    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_bookmarks')->withTimestamps();
    }

    // Comments gana, likes gana, saha dan inna user like / save karalada kiyala ganna
    // Login nathi visitor nam userId = 0 (kisima user kenekta match wenne na) => liked / bookmarked = false
    public function scopeWithInteractions(Builder $query): Builder
    {
        $userId = auth()->id() ?? 0;

        return $query
            ->withCount(['comments', 'likedBy'])
            ->withExists([
                'likedBy as liked'           => fn ($q) => $q->where('users.id', $userId),
                'bookmarkedBy as bookmarked' => fn ($q) => $q->where('users.id', $userId),
            ]);
    }

    /*Post
 ├── belongsTo → User
 ├── belongsTo → Category
 ├── hasMany → Comments
 ├── belongsToMany → Liked Users
 └── belongsToMany → Bookmarked Users*/
}