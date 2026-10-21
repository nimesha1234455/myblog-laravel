<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
//comment save /update karahha use KARAN FIELDS
{
    protected $fillable = ['post_id', 'user_id', 'body'];

    public function post(): BelongsTo
    {
        // withTrashed(): Trash karapu post ekk comment comment  error ekk  nathiw hoyynn
        return $this->belongsTo(Post::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}