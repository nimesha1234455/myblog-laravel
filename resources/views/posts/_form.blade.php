@php
    $editing     = isset($post);
    $isPublished = $editing && $post->status === 'published';

    // CHANGED: meta fields (SEO) pennanne admin ta witharai
    $canMeta         = auth()->check() && auth()->user()->role === 'admin';
    $metaDescription = old('meta_description', $post->meta_description ?? '');
    $metaKeywords    = old('meta_keywords', $post->meta_keywords ?? '');
    $metaOpen        = $metaDescription !== '' || $metaKeywords !== ''
                       || $errors->has('meta_description') || $errors->has('meta_keywords');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="post-form"
      class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-medium">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}"
                   placeholder="Enter post title"
                   class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Category <span class="text-red-500">*</span></label>
            <select name="category_id"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected(old('category_id', $post->category_id ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-sm font-medium">
            Cover Image @unless($editing)<span class="text-red-500">*</span>@endunless
        </label>

        <img id="preview"
             src="{{ $editing ? asset('storage/' . $post->cover_image) : '' }}"
             class="mb-3 h-40 rounded-lg object-cover {{ $editing ? '' : 'hidden' }}">

        <input type="file" name="cover_image" id="cover_image" accept=".png,.jpg,.jpeg,.webp"
               class="block w-full rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-500">
        <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP (Max 15MB){{ $editing ? ' · Leave empty to keep the current image' : '' }}</p>
        @error('cover_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-sm font-medium">Content <span class="text-red-500">*</span></label>
        <div id="editor" class="h-64 bg-white"></div>
        <input type="hidden" name="content" id="content" value="{{ old('content', $post->content ?? '') }}">
        @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- CHANGED: SEO / Meta details (optional) - admin ta witharai pennanne.
         Hisa thibboth post eka ganna wenama content eken auto hadenawa --}}
    @if ($canMeta)
        <details class="mt-6 rounded-lg border border-slate-200 p-4" @if ($metaOpen) open @endif>
            <summary class="cursor-pointer text-sm font-medium">
                SEO / Meta details <span class="font-normal text-slate-400">(optional)</span>
            </summary>

            <p class="mt-2 text-xs text-slate-400">
                Google, WhatsApp, Facebook walata pena preview eka. Hisa thibboth content eken auto hadenawa.
            </p>

            <div class="mt-4">
                <div class="mb-1 flex items-center justify-between">
                    <label for="meta_description" class="block text-sm font-medium">Meta description</label>
                    <span class="text-xs text-slate-400"><span id="meta_count">{{ mb_strlen($metaDescription) }}</span> / 160</span>
                </div>
                <textarea name="meta_description" id="meta_description" rows="3" maxlength="160"
                          placeholder="Short summary of this post (max 160 characters)"
                          class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ $metaDescription }}</textarea>
                @error('meta_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="meta_keywords" class="mb-1 block text-sm font-medium">Meta keywords</label>
                <input type="text" name="meta_keywords" id="meta_keywords" maxlength="255"
                       value="{{ $metaKeywords }}"
                       placeholder="laravel, blog, programming"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-slate-400">Comma (,) ekakin wen karanna.</p>
                @error('meta_keywords') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </details>
    @endif

    @error('status') <p class="mt-4 text-sm text-red-600">{{ $message }}</p> @enderror

    {{-- CHANGED: buttons dekak, dekema "status" field eka yawanawa --}}
    <div class="mt-6 flex flex-wrap justify-end gap-3">
        <a href="{{ $editing ? route('posts.show', $post) : route('posts.index') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">Cancel</a>

        <button type="submit" name="status" value="draft"
                class="rounded-lg border border-amber-400 bg-amber-50 px-5 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100">
            {{ $isPublished ? 'Move to Drafts' : 'Save as Draft' }}
        </button>

        <button type="submit" name="status" value="published"
                class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">
            {{ $isPublished ? 'Update' : 'Publish' }}
        </button>
    </div>
</form>

<script>
    // Meta description akuru gana (admin ta witharai thiyenne, eka nisa element eka nathnam skip karanawa)
    (function () {
        const metaDesc  = document.getElementById('meta_description');
        const metaCount = document.getElementById('meta_count');
        if (!metaDesc || !metaCount) return;

        const updateMetaCount = () => { metaCount.textContent = metaDesc.value.length; };
        metaDesc.addEventListener('input', updateMetaCount);
        updateMetaCount();
        window.addEventListener('load', updateMetaCount);
        window.addEventListener('pageshow', updateMetaCount);
    })();

    // Rich text editor
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Write your content here...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean'],
            ],
        },
    });

    const contentInput = document.getElementById('content');
    quill.root.innerHTML = contentInput.value;

    document.getElementById('post-form').addEventListener('submit', () => {
        contentInput.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
    });

    // Image preview
    document.getElementById('cover_image').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    });
</script>