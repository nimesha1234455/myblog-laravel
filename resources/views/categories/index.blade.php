<x-blog-layout title="Categories">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Categories</h1>
        <p class="text-sm text-slate-500">Organize your posts by category</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Add category --}}
        <div class="h-fit rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 font-bold">Add New Category</h2>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Category name"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <button type="submit"
                        class="mt-3 w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    + Add Category
                </button>
            </form>
        </div>

        {{-- List --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-200 px-6 py-4 font-bold">
                All Categories ({{ $categories->count() }})
            </div>

            @forelse ($categories as $category)
                <div x-data="{ editing: false }"
                     class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-4 last:border-0">

                    {{-- View mode --}}
                    <div x-show="!editing" class="flex items-center gap-3">
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
                            {{ $category->name }}
                        </span>
                        <span class="text-xs text-slate-500">
                            {{ $category->posts_count }} {{ \Illuminate\Support\Str::plural('post', $category->posts_count) }}
                        </span>
                    </div>

                    {{-- Edit mode --}}
                    <form x-show="editing" x-cloak method="POST"
                          action="{{ route('categories.update', $category) }}"
                          class="flex flex-1 items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $category->name }}"
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700">Save</button>
                        <button type="button" @click="editing = false"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm hover:bg-slate-50">Cancel</button>
                    </form>

                    {{-- Actions --}}
                    <div x-show="!editing" class="flex items-center gap-2">
                        <button type="button" @click="editing = true"
                                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">Edit</button>

                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                              data-confirm="This category will be deleted permanently."
                              data-confirm-title="Delete category?"
                              data-confirm-button="Delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-slate-500">No categories yet.</p>
            @endforelse
        </div>
    </div>
</x-blog-layout>