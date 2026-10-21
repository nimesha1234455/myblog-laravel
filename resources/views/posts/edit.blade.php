<x-blog-layout title="Edit Post">
    <x-slot:head>
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    </x-slot:head>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Edit Post</h1>
        <p class="text-sm text-slate-500">Update your blog post</p>
    </div>

    @include('posts._form', ['action' => route('posts.update', $post), 'post' => $post])
</x-blog-layout>