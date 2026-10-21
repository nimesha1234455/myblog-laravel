<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Setting;
use App\Observers\PostObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /*
      Register any application services.
     */
    public function register(): void
    {
        //
    }

    /*
      Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->applySmtpSettings();
        $this->registerPermissions();
        $this->registerActivityLogging();
    }

    /*
      Activity Log: post observer + login / logout / register events
     */
    private function registerActivityLogging(): void
    {
        Post::observe(PostObserver::class);

        Event::listen(Login::class, function (Login $e) {
            ActivityLog::record('auth.login', $e->user->name . ' logged in', $e->user, $e->user->id);
        });

        Event::listen(Logout::class, function (Logout $e) {
            if ($e->user) {
                ActivityLog::record('auth.logout', $e->user->name . ' logged out', $e->user, $e->user->id);
            }
        });

        Event::listen(Registered::class, function (Registered $e) {
            ActivityLog::record('auth.register', 'New account registered: ' . $e->user->email, $e->user, $e->user->id);
        });
    }

    /*
      Permission Manager: admin ta okkoma allow, anith roles walata
      permissions table eke thiyena ewa Gate ekak widiyata register karanawa.
     */
    private function registerPermissions(): void
    {
        // Admin ta hama permission ekakma allow
        Gate::before(fn ($user) => $user->role === 'admin' ? true : null);

        try {
            if (! Schema::hasTable('permissions')) {
                return;
            }

            foreach (Permission::pluck('name') as $name) {
                Gate::define($name, fn ($user) => $user->hasPermission($name));
            }
        } catch (\Throwable $e) {
            // migrate wenakal (table nathi welawe) ignore karanawa
        }
    }

    /*
      Admin panel eke save karapu SMTP settings, .env mail config eka override karanawa.
     */
    private function applySmtpSettings(): void
    {
        try {
            if (! Setting::get('smtp.host')) {
                return;
            }

            $enc = Setting::get('smtp.encryption', 'tls');

            $password = null;
            if ($saved = Setting::get('smtp.password')) {
                $password = Crypt::decryptString($saved);
            }

            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => Setting::get('smtp.host'),
                'mail.mailers.smtp.port'       => (int) Setting::get('smtp.port', 587),
                'mail.mailers.smtp.username'   => Setting::get('smtp.username'),
                'mail.mailers.smtp.password'   => $password,
                // Laravel 11+ : scheme  |  Laravel 10 : encryption
                'mail.mailers.smtp.scheme'     => $enc === 'ssl' ? 'smtps' : 'smtp',
                'mail.mailers.smtp.encryption' => $enc === 'none' ? null : $enc,
                'mail.from.address'            => Setting::get('smtp.from_address'),
                'mail.from.name'               => Setting::get('smtp.from_name'),
            ]);
        } catch (\Throwable $e) {
            // settings table eka thama hadala nathnam (migrate wenakal) ignore karanawa
        }
    }
}