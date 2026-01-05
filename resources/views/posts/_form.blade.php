@php
    $isEdit = isset($post);
@endphp

<form action="{{ $isEdit ? route('posts.update', $post->id) : route('posts.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Title --}}
    <div>
        <label for="title" class="block mb-1 font-semibold text-[#4B0082]">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $isEdit ? $post->title : '') }}" class="w-full p-3 border border-[#4B0082] rounded shadow-sm focus:ring-2 focus:ring-[#4B0082] focus:outline-none">
        @error('title')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    {{-- Category --}}
    <div>
        <label for="category" class="block mb-1 font-semibold text-[#4B0082]">Category</label>
        <input type="text" name="category" id="category" value="{{ old('category', $isEdit ? $post->category : '') }}" class="w-full p-3 border border-[#4B0082] rounded shadow-sm focus:ring-2 focus:ring-[#4B0082] focus:outline-none">
        @error('category')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    {{-- Content --}}
    <div>
        <label for="content" class="block mb-1 font-semibold text-[#4B0082]">Content</label>
        <textarea name="content" id="content" rows="6" class="w-full p-3 border border-[#4B0082] rounded shadow-sm focus:ring-2 focus:ring-[#4B0082] focus:outline-none">{{ old('content', $isEdit ? $post->content : '') }}</textarea>
        @error('content')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    {{-- Image Upload --}}
    <div>
        <label class="block mb-2 font-semibold text-[#4B0082]" for="image">Image</label>

        <label for="image" class="flex flex-col items-center justify-center h-40 border-2 border-dashed border-[#4B0082] rounded-lg cursor-pointer hover:border-indigo-700 hover:bg-indigo-50 transition-all shadow-sm hover:shadow-md p-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#4B0082] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v6m0-6l-4 4m4-4l4 4M12 3v9"/>
            </svg>
            <span class="text-[#4B0082] text-sm font-medium">Click or drag file to upload</span>
            <input type="file" name="image" id="image" class="hidden">
        </label>

        @if($isEdit && $post->image)
            <img src="{{ asset('storage/'.$post->image) }}" alt="Post Image" class="mt-4 w-48 h-48 object-cover rounded-lg border border-[#4B0082] shadow-md">
        @endif

        @error('image')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="w-full px-4 py-3 bg-green-500 text-white font-semibold rounded hover:bg-green-600 shadow hover:shadow-md transition">
        {{ $isEdit ? 'Update Post' : 'Create Post' }}
    </button>
</form>
