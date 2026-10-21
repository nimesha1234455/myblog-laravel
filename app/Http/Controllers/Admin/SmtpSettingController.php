<?php
// app/Http/Controllers/Admin/SmtpSettingController.php
// (Admin folder eka nathnam hadanna, nathnam namespace/path eka wenas karanna)

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class SmtpSettingController extends Controller
{
    private function guard(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }

    public function edit()
    {
        $this->guard();

        return view('admin.smtp');
    }

    public function update(Request $request)
    {
        $this->guard();

        $data = $request->validate([
            'host'         => 'required|string|max:255',
            'port'         => 'required|integer|min:1|max:65535',
            'encryption'   => 'required|in:tls,ssl,none',
            'username'     => 'nullable|string|max:255',
            'password'     => 'nullable|string|max:255',
            'from_address' => 'required|email|max:255',
            'from_name'    => 'required|string|max:255',
        ]);

        $save = [
            'smtp.host'         => $data['host'],
            'smtp.port'         => $data['port'],
            'smtp.encryption'   => $data['encryption'],
            'smtp.username'     => $data['username'] ?? null,
            'smtp.from_address' => $data['from_address'],
            'smtp.from_name'    => $data['from_name'],
        ];

        // Password eka type karala nathnam, palaya ekama thiyenawa
        if ($request->filled('password')) {
            $save['smtp.password'] = Crypt::encryptString($data['password']);
        }

        Setting::setMany($save);

        return back()->with('success', 'SMTP settings saved.');
    }

    // CHANGED: akuru witharak email eka wenuwata, HTML design ekak (emails/smtp-test.blade.php) yawanawa
    public function test(Request $request)
    {
        $this->guard();

        $request->validate(['test_email' => 'required|email']);

        try {
            $appName = config('app.name', 'MyBlog');

            // Save karapu settings walin ganna. Nathnam .env / config eken
            $host       = Setting::get('smtp.host') ?: config('mail.mailers.smtp.host');
            $port       = Setting::get('smtp.port') ?: config('mail.mailers.smtp.port');
            $encryption = Setting::get('smtp.encryption') ?: 'tls';

            $encryptionLabel = match ($encryption) {
                'ssl'   => 'SSL',
                'tls'   => 'TLS',
                default => 'None',
            };

            $data = [
                'appName'    => $appName,
                'name'       => auth()->user()->name,
                'host'       => $host,
                'port'       => $port,
                'encryption' => $encryptionLabel,
                'sentAt'     => now()->format('Y-m-d H:i'),
                'url'        => route('posts.index'),
            ];

            $html = view('emails.smtp-test', $data)->render();

            // Html eka support nokarana email apps walata akuru witharak version ekak
            $plain = "Hello {$data['name']},\n\n"
                   . "Great news! Your SMTP settings are working. This is a test email sent from {$appName}.\n\n"
                   . "SMTP host: {$data['host']}\n"
                   . "Port: {$data['port']}\n"
                   . "Encryption: {$data['encryption']}\n"
                   . "Sent at: {$data['sentAt']}\n\n"
                   . "Open {$appName}: {$data['url']}\n";

            Mail::html($html, function ($m) use ($request, $appName, $plain) {
                $m->to($request->test_email)
                  ->subject($appName . ' SMTP test')
                  ->text($plain);
            });

            return back()->with('success', 'Test email sent to ' . $request->test_email);
        } catch (\Throwable $e) {
            return back()->with('error', 'Sending failed: ' . $e->getMessage());
        }
    }
}