{{-- resources/views/admin/activity-logs.blade.php --}}
<x-blog-layout title="Activity Log">
    <x-slot:sidebar>
        <x-admin-sidebar />
    </x-slot:sidebar>

    <div class="max-w-6xl space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Activity Log</h1>
                <p class="text-sm text-slate-500">A record of logins, post changes and other actions across the site.</p>
            </div>

            <form method="POST" action="{{ route('admin.activity-logs.clear') }}"
                  onsubmit="return confirm('Delete all logs older than 30 days? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600">
                    Clear old logs
                </button>
            </form>
        </div>

        {{-- Filter --}}
        <form method="GET"
              class="flex flex-wrap gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <select name="action" class="rounded-lg border-slate-200 text-sm">
                <option value="">All actions</option>
                <option value="auth" @selected(request('action') === 'auth')>Login / Logout / Register</option>
                <option value="post" @selected(request('action') === 'post')>Posts</option>
            </select>

            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..."
                   class="min-w-[200px] flex-1 rounded-lg border-slate-200 text-sm">

            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Filter
            </button>

            @if (request()->hasAny(['action', 'q']))
                <a href="{{ route('admin.activity-logs') }}"
                   class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-medium">When</th>
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                        <th class="px-4 py-3 font-medium">Description</th>
                        <th class="px-4 py-3 font-medium">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        @php
                            $color = match (true) {
                                str_contains($log->action, 'deleted') => 'bg-red-100 text-red-700',
                                str_contains($log->action, 'created'),
                                $log->action === 'auth.register'      => 'bg-green-100 text-green-700',
                                str_starts_with($log->action, 'auth') => 'bg-blue-100 text-blue-700',
                                default                               => 'bg-yellow-100 text-yellow-700',
                            };
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3" title="{{ $log->created_at }}">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $color }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                No activity yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $logs->links() }}</div>
    </div>
</x-blog-layout>