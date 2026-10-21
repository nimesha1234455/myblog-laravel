<x-blog-layout title="Permissions">
    <x-slot:sidebar>
        <x-admin-sidebar />
    </x-slot:sidebar>

    @php
        // Delete karanna be mul permissions (PermissionController::PROTECTED)
        $protected = \App\Http\Controllers\PermissionController::PROTECTED;
    @endphp

    <div class="space-y-6">

        {{-- Header + Add Permission button eka --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Permission Manager</h1>
                <p class="text-sm text-slate-500">Control what each role can do. Admin always has full access.</p>
            </div>

            {{-- Validation error ekak awoth modal eka aye open wela thiyenawa --}}
            <div x-data="{ open: {{ $errors->has('name') || $errors->has('label') || $errors->has('group') ? 'true' : 'false' }} }">
                <button type="button" @click="open = true"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    + Add Permission
                </button>

                {{-- Add Permission modal eka --}}
                <div x-show="open" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                     @keydown.escape.window="open = false">
                    <div @click.outside="open = false"
                         class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-xl">
                        <h2 class="mb-4 text-lg font-bold">Add Permission</h2>

                        <form method="POST" action="{{ route('permissions.store') }}" class="space-y-4">
                            @csrf

                            {{-- Label: page eke pennana nama --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium">Label <span class="text-red-500">*</span></label>
                                <input type="text" name="label" value="{{ old('label') }}" placeholder="Write posts"
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('label') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Key: Gate / can: walata paawichchi wena nama (permissions.name column eka) --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium">Key <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="posts.create"
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-slate-400">Format: <code>area.action</code> (e.g. posts.create)</p>
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Group: thiyena groups datalist eken suggest wenawa --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium">Group <span class="text-red-500">*</span></label>
                                <input type="text" name="group" list="permission-groups" value="{{ old('group') }}" placeholder="POSTS"
                                       class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <datalist id="permission-groups">
                                    @foreach ($permissions->keys() as $g)
                                        <option value="{{ $g }}">
                                    @endforeach
                                </datalist>
                                @error('group') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="open = false"
                                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">Cancel</button>
                                <button type="submit"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Role permissions matrix eka --}}
        <form method="POST" action="{{ route('permissions.roles.update', 'reader') }}"
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            @csrf
            @method('PUT')

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left">
                            <th class="py-2 pr-4 font-semibold">Permission</th>
                            <th class="w-24 px-4 py-2 text-center font-semibold">Admin</th>
                            <th class="w-24 px-4 py-2 text-center font-semibold">Reader</th>
                            <th class="w-16 px-2 py-2"></th>
                        </tr>
                    </thead>

                    {{-- Group ekin ekin permissions pennanawa --}}
                    @foreach ($permissions as $group => $items)
                        <tbody>
                            <tr>
                                <td colspan="4" class="pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    {{ $group }}
                                </td>
                            </tr>
                            @foreach ($items as $perm)
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 pr-4">
                                        <p class="font-medium">{{ $perm->label }}</p>
                                        <p class="text-xs text-slate-400">{{ $perm->name }}</p>
                                    </td>

                                    {{-- Admin ta hama permission ekakma thiyenawa (lock karala) --}}
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" checked disabled class="rounded border-slate-300 text-blue-600 opacity-60">
                                    </td>

                                    {{-- Reader ta tick karapu ewa witharai --}}
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                               @checked(in_array($perm->id, $readerIds))
                                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    </td>

                                    {{-- Delete button eka (mul permissions walata pennanne na) --}}
                                    <td class="px-2 py-2 text-center">
                                        @unless (in_array($perm->name, $protected, true))
                                            <button type="submit" form="delete-perm-{{ $perm->id }}" title="Delete permission"
                                                    class="rounded p-1 text-slate-400 hover:bg-red-50 hover:text-red-600">
                                                🗑️
                                            </button>
                                        @endunless
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Save permissions
                </button>
            </div>
        </form>

        {{-- Delete forms (matrix form eken pitath thiyenne, form ekak athule form ekak danna be nisa).
             Button walata form="..." attribute eken sambandha wenawa. --}}
        @foreach ($permissions->flatten() as $perm)
            @unless (in_array($perm->name, $protected, true))
                <form id="delete-perm-{{ $perm->id }}" method="POST"
                      action="{{ route('permissions.destroy', $perm) }}"
                      data-confirm="'{{ $perm->name }}' permission eka sadahatama delete wenawa. Reader la ge access ekath nathi wenawa."
                      data-confirm-title="Delete permission?"
                      data-confirm-button="Delete">
                    @csrf
                    @method('DELETE')
                </form>
            @endunless
        @endforeach

        {{-- Users saha ewage roles --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 text-lg font-bold">Users</h2>

            <div class="space-y-3">
                @foreach ($users as $user)
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <x-avatar :user="$user" size="h-9 w-9" />
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>

                        {{-- Thamange role eka thamanta wenas karanna be --}}
                        @if ($user->id === auth()->id())
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                {{ ucfirst($user->role) }} (you)
                            </span>
                        @else
                            <form method="POST" action="{{ route('permissions.users.update', $user) }}">
                                @csrf
                                @method('PUT')
                                <select name="role" onchange="this.form.submit()"
                                        class="rounded-lg border-slate-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-blog-layout>