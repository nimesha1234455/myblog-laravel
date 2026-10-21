<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function allCached(): array
    {
        return Cache::rememberForever('settings', fn () => static::pluck('value', 'key')->toArray());
    }

    public static function get(string $key, $default = null)
    {
        return static::allCached()[$key] ?? $default;
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('settings');
    }
}