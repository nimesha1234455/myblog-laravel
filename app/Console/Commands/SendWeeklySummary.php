<?php

namespace App\Console\Commands;

use App\Mail\WeeklySummary;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklySummary extends Command
{
    protected $signature = 'myblog:weekly {--to= : Send only to this email address (for testing)}';

    // The old name keeps working too
    protected $aliases = ['report:weekly-summary'];

    protected $description = 'Email the weekly blog summary to all admins';

    public function handle(): int
    {
        $since = now()->subDays(7);

        $data = [
            'from'           => $since->format('M d, Y'),
            'to'             => now()->format('M d, Y'),
            'new_posts'      => Post::where('created_at', '>=', $since)->count(),
            'new_users'      => User::where('created_at', '>=', $since)->count(),
            'pending'        => Post::where('status', 'pending')->count(),
            'total_views'    => (int) Post::sum('views'),
            'activity_count' => ActivityLog::where('created_at', '>=', $since)->count(),
            'top_posts'      => Post::where('status', 'published')
                                    ->orderByDesc('views')
                                    ->take(3)
                                    ->get(),
        ];

        $recipients = $this->option('to')
            ? collect([$this->option('to')])
            : User::where('role', 'admin')->pluck('email');

        if ($recipients->isEmpty()) {
            $this->warn('No admin users found. Nothing sent.');
            return self::SUCCESS;
        }

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new WeeklySummary($data));
                $this->info("Sent to {$email}");
            } catch (\Throwable $e) {
                $this->error("Failed for {$email}: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}