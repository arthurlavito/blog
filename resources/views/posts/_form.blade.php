@php
    // Check if we are in Edit mode by checking if $post exists and is saved in DB
    $isEdit = isset($post) && $post->exists;
@endphp

{{-- 
    Dynamic Form Header: 
    - POST to 'store' for new posts
    - POST to 'update' for existing posts (Laravel handles the PUT via @method)
--}}
<form action="{{ $isEdit ? route('posts.update', $post) : route('posts.store') }}" 
      method="POST" 
      enctype="multipart/form-data" 
      class="space-y-6">
    
    @csrf
    
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Title Input --}}
    <div>
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Article Title</label>
        <input type="text" 
               name="title" 
               value="{{ old('title', $post->title ?? '') }}" 
               required
               class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm"
               placeholder="Enter a catchy headline...">
        @error('title') 
            <p class="text-rose-500 text-[10px] mt-2 font-bold uppercase italic tracking-wider">{{ $message }}</p> 
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Category Selection --}}
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Category</label>
            <div class="relative">
                <select name="category" 
                        required
                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm appearance-none cursor-pointer">
                    <option value="" disabled {{ !isset($post) ? 'selected' : '' }}>Select Category</option>
                    @foreach(['Action', 'News', 'Review', 'Updates', 'Community'] as $cat)
                        <option value="{{ $cat }}" {{ (old('category', $post->category ?? '') == $cat) ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
                {{-- Custom Arrow Icon for Select --}}
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            @error('category') 
                <p class="text-rose-500 text-[10px] mt-2 font-bold uppercase italic tracking-wider">{{ $message }}</p> 
            @enderror
        </div>

        {{-- Image Upload --}}
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Cover Image</label>
            <input type="file" 
                   name="image" 
                   class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-xs focus:ring-2 focus:ring-[#4B0082] transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-[#4B0082] hover:file:bg-indigo-100 cursor-pointer">
            @error('image') 
                <p class="text-rose-500 text-[10px] mt-2 font-bold uppercase italic tracking-wider">{{ $message }}</p> 
            @enderror
        </div>
    </div>

    {{-- Current Image Preview (If Editing) --}}
    @if($isEdit && $post->image)
        <div class="p-4 bg-gray-50 rounded-3xl border border-gray-100 inline-block">
            <p class="text-[10px] font-black uppercase text-gray-400 mb-3 flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                Currently Active Image
            </p>
            <img src="{{ asset('storage/' . $post->image) }}" 
                 class="w-48 h-28 object-cover rounded-2xl border-2 border-white shadow-sm">
        </div>
    @endif

    {{-- Content Textarea --}}
    <div>
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Article Content</label>
        <textarea name="content" 
                  rows="12" 
                  required
                  class="w-full bg-gray-50 border-none rounded-[2rem] px-6 py-5 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm leading-relaxed"
                  placeholder="Tell your story here...">{{ old('content', $post->content ?? '') }}</textarea>
        @error('content') 
            <p class="text-rose-500 text-[10px] mt-2 font-bold uppercase italic tracking-wider">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Form Actions --}}
    <div class="pt-6 flex flex-col md:flex-row items-center gap-4">
        <button type="submit" 
                class="w-full md:w-auto px-12 py-4 bg-[#4B0082] text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-900 transition-all shadow-xl shadow-indigo-100 active:scale-95">
            {{ $isEdit ? 'Update Masterpiece' : 'Publish Article' }}
        </button>

        <a href="{{ $isEdit ? route('posts.show', $post) : route('posts.index') }}" 
           class="w-full md:w-auto px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-200 transition-all text-center">
            Discard Changes
        </a>
    </div>
</form>