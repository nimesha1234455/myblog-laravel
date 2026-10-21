<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->error('No users found. Register an account first.');
            return;
        }

        $posts = [
            [
                'category' => 'Technology',
                'title'    => 'The Future of Web Development',
                'colors'   => ['#0ea5e9', '#4f46e5'],
                'content'  => '<p>Web development is constantly evolving, and the future looks more exciting than ever. With new technologies and tools emerging every year, developers have more opportunities to build fast, secure and user-friendly applications.</p>'
                    . '<h2>1. The Rise of AI-Powered Development</h2>'
                    . '<p>Artificial Intelligence is already changing the way developers work. From code completion to automated testing, AI tools are helping us build better software, faster.</p>'
                    . '<h2>2. More Focus on Performance</h2>'
                    . '<p>Users expect faster and smoother experiences. Edge computing, serverless architecture and optimized frameworks will play a bigger role in the coming years.</p>',
            ],
            [
                'category' => 'Lifestyle',
                'title'    => 'Simple Habits for a Better Life',
                'colors'   => ['#f59e0b', '#ec4899'],
                'content'  => '<p>Small habits can make a big difference. Here are some simple daily habits to improve your productivity and happiness.</p>'
                    . '<h2>1. Start Your Day Early</h2>'
                    . '<p>A calm morning gives you time to plan your day, exercise or simply enjoy a quiet cup of tea before the world wakes up.</p>'
                    . '<h2>2. Take Short Breaks</h2>'
                    . '<p>Stepping away from your screen for a few minutes every hour keeps your mind fresh and your eyes rested.</p>',
            ],
            [
                'category' => 'Programming',
                'title'    => "Laravel 13: A Beginner's Guide",
                'colors'   => ['#0f172a', '#2563eb'],
                'content'  => '<p>Laravel brings many new features and improvements. In this guide, we will cover the basics to get you started.</p>'
                    . '<h2>Why Laravel?</h2>'
                    . '<p>Laravel gives you routing, an ORM called Eloquent, authentication and a powerful templating engine called Blade, all in one framework.</p>'
                    . '<h2>Your First Steps</h2>'
                    . '<ul><li>Install PHP, Composer and Node.js</li><li>Create a project with Composer</li><li>Build your first route, controller and view</li></ul>',
            ],
            [
                'category' => 'Travel',
                'title'    => 'Exploring the Beautiful Sri Lanka',
                'colors'   => ['#10b981', '#0891b2'],
                'content'  => '<p>Sri Lanka is a small island with big dreams. From beaches to mountains, there is so much to explore.</p>'
                    . '<h2>Beaches in the South</h2>'
                    . '<p>Golden sand, warm water and fresh seafood make the southern coast a perfect place to relax.</p>'
                    . '<h2>The Hill Country</h2>'
                    . '<p>Misty tea estates, waterfalls and scenic train rides make the hill country unforgettable.</p>',
            ],
        ];

        Storage::disk('public')->makeDirectory('posts');

        foreach ($posts as $i => $item) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($item['category'])],
                ['name' => $item['category']]
            );

            $slug = Str::slug($item['title']);

            $post = Post::withTrashed()->firstOrNew(['slug' => $slug]);
            $post->fill([
                'user_id'     => $user->id,
                'category_id' => $category->id,
                'title'       => $item['title'],
                'slug'        => $slug,
                'cover_image' => $this->makeCover($slug, $item['category'], $item['colors']),
                'content'     => $item['content'],
            ]);
            $post->created_at = now()->subDays($i * 2);
            $post->save();
        }

        $this->command->info('Sample posts created.');
    }

    private function makeCover(string $slug, string $label, array $colors): string
    {
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0" stop-color="%s"/><stop offset="1" stop-color="%s"/>'
            . '</linearGradient></defs>'
            . '<rect width="1200" height="630" fill="url(#g)"/>'
            . '<text x="600" y="340" font-family="Arial, sans-serif" font-size="72" font-weight="bold" fill="#ffffff" text-anchor="middle">%s</text>'
            . '</svg>',
            $colors[0], $colors[1], $label
        );

        $path = "posts/seed-{$slug}.svg";
        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}