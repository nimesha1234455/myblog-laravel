{{-- resources/views/admin/smtp.blade.php --}}
<x-blog-layout title="SMTP Settings">
    <x-slot:sidebar>
        <x-admin-sidebar />
    </x-slot:sidebar>

    @php
        // NOTE: @php ekak athule "use" danna ba, ethakota full class name eka use karanawa
        $setting = \App\Models\Setting::class;
        $v = fn ($k, $d = '') => old(str_replace('smtp.', '', $k), $setting::get($k, $d));
    @endphp

    <div class="max-w-2xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold">SMTP Settings</h1>
            <p class="text-sm text-slate-500">Email (notifications, password reset) yawanna use karana mail server eka.</p>
        </div>

        {{-- Settings form --}}
        <form method="POST" action="{{ route('admin.smtp.update') }}"
              class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium">SMTP Host</label>
                    <input type="text" name="host" value="{{ $v('smtp.host') }}" placeholder="smtp.gmail.com"
                           class="w-full rounded-lg border-slate-200 text-sm">
                    @error('host') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Port</label>
                    <input type="number" name="port" value="{{ $v('smtp.port', 587) }}"
                           class="w-full rounded-lg border-slate-200 text-sm">
                    @error('port') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Encryption</label>
                @php $enc = $v('smtp.encryption', 'tls'); @endphp
                <select name="encryption" class="w-full rounded-lg border-slate-200 text-sm">
                    <option value="tls"  @selected($enc === 'tls')>TLS (port 587)</option>
                    <option value="ssl"  @selected($enc === 'ssl')>SSL (port 465)</option>
                    <option value="none" @selected($enc === 'none')>None</option>
                </select>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Username</label>
                    <input type="text" name="username" value="{{ $v('smtp.username') }}" autocomplete="off"
                           class="w-full rounded-lg border-slate-200 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Password</label>
                    <input type="password" name="password" autocomplete="new-password"
                           placeholder="{{ $setting::get('smtp.password') ? '•••••••• (wenas karanna nam witharak type karanna)' : '' }}"
                           class="w-full rounded-lg border-slate-200 text-sm">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">From address</label>
                    <input type="email" name="from_address" value="{{ $v('smtp.from_address') }}" placeholder="noreply@myblog.com"
                           class="w-full rounded-lg border-slate-200 text-sm">
                    @error('from_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">From name</label>
                    <input type="text" name="from_name" value="{{ $v('smtp.from_name', 'MyBlog') }}"
                           class="w-full rounded-lg border-slate-200 text-sm">
                    @error('from_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Save settings
            </button>
        </form>

        {{-- Test email --}}
        <form method="POST" action="{{ route('admin.smtp.test') }}"
              class="space-y-3 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="font-bold">Send test email</h2>
            <p class="text-xs text-slate-500">Palamuwen settings save karanna, passe test eka yawanna.</p>
            <div class="flex gap-3">
                <input type="email" name="test_email" value="{{ auth()->user()->email }}" required
                       class="w-full rounded-lg border-slate-200 text-sm">
                <button type="submit"
                        class="shrink-0 rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium hover:bg-slate-50">
                    Send test
                </button>
            </div>
        </form>
    </div>
</x-blog-layout>