@if(isset($categories) && $categories->count())
<div class="bg-gray-100 border-b">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex flex-wrap gap-2 items-center">
            <span class="text-sm font-semibold text-gray-600 mr-2">
                Categories:
            </span>

            <a href="{{ route('posts.index') }}"
               class="px-3 py-1 rounded-full text-sm
               {{ Route::currentRouteName() === 'posts.index'
                    ? 'bg-indigo-600 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-200' }}">
                All
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('categories.show', $cat) }}"
                   class="px-3 py-1 rounded-full text-sm
                   {{ Route::currentRouteName() === 'categories.show' && isset($activeCategory) && $activeCategory->id === $cat->id
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-200' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
