<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixCoverImages extends Command
{
    protected $signature = 'images:fix {--inset=5 : Percent to crop from every edge}';

    protected $description = 'Crop baked-in borders from cover images and resize them to 1200x630';

    public function handle(): int
    {
        $inset  = max(0, min(15, (float) $this->option('inset'))) / 100;
        $public = Storage::disk('public');
        $backup = Storage::disk('local');

        $posts = Post::withTrashed()->whereNotNull('cover_image')->get();

        foreach ($posts as $post) {
            $path = $post->cover_image;

            if (! $public->exists($path)) {
                $this->warn("Missing file: {$path}");
                continue;
            }

            // Original eka kalin backup karanawa; command eka aye run kalath original ekenma wada karanawa
            $backupPath = 'cover-backups/' . $path;
            if (! $backup->exists($backupPath)) {
                $backup->put($backupPath, $public->get($path));
            }

            $img = @imagecreatefromstring($backup->get($backupPath));
            if (! $img) {
                $this->warn("Cannot read image: {$path}");
                continue;
            }

            $w  = imagesx($img);
            $h  = imagesy($img);
            $cx = (int) ($w * $inset);
            $cy = (int) ($h * $inset);
            $sw = $w - 2 * $cx;
            $sh = $h - 2 * $cy;

            // 1200x630 ratio ekata center crop
            $tw    = 1200;
            $th    = 630;
            $ratio = $tw / $th;

            if ($sw / $sh > $ratio) {
                $nw  = (int) ($sh * $ratio);
                $cx += (int) (($sw - $nw) / 2);
                $sw  = $nw;
            } else {
                $nh  = (int) ($sw / $ratio);
                $cy += (int) (($sh - $nh) / 2);
                $sh  = $nh;
            }

            $out = imagecreatetruecolor($tw, $th);
            imagecopyresampled($out, $img, 0, 0, $cx, $cy, $tw, $th, $sw, $sh);

            ob_start();
            switch (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                case 'png':
                    imagepng($out, null, 6);
                    break;
                case 'webp':
                    imagewebp($out, null, 88);
                    break;
                default:
                    imagejpeg($out, null, 88);
            }
            $public->put($path, ob_get_clean());

            $this->info("Fixed: {$path}");
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}