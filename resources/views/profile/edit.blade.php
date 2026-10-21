<x-blog-layout title="Profile">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Profile</h1>
        <p class="text-sm text-slate-500">Manage your account settings</p>
    </div>

    <div class="max-w-3xl space-y-6">

        {{-- Profile photo --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-medium text-gray-900">Profile Photo</h2>
            <p class="mt-1 text-sm text-gray-600">Upload a photo (PNG, JPG, WEBP · max 2MB).</p>

            <div class="mt-5 flex items-center gap-6" x-data="{ preview: null }">

                {{-- Preview / current photo --}}
                <div>
                    <template x-if="preview">
                        <img :src="preview" class="h-20 w-20 rounded-full object-cover ring-2 ring-blue-100">
                    </template>
                    <div x-show="!preview">
                        <x-avatar :user="auth()->user()" size="h-20 w-20" text="text-2xl" />
                    </div>
                </div>

                <div>
                    <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data"
                          class="flex flex-wrap items-center gap-3">
                        @csrf
                        <input type="file" name="avatar" accept=".png,.jpg,.jpeg,.webp" required
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                               class="text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-slate-200">
                        <button type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Upload
                        </button>
                    </form>

                    <x-input-error class="mt-2" :messages="$errors->avatar->get('avatar')" />

                    @if (session('status') === 'avatar-updated')
                        <p class="mt-2 text-sm text-green-600">Photo updated.</p>
                    @endif
                    @if (session('status') === 'avatar-removed')
                        <p class="mt-2 text-sm text-green-600">Photo removed.</p>
                    @endif

                    @if (auth()->user()->avatar)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}" class="mt-2"
                              data-confirm="Your profile photo will be removed and your initial will be shown instead."
                              data-confirm-title="Remove photo?"
                              data-confirm-button="Remove">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">Remove photo</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>
</x-blog-layout>