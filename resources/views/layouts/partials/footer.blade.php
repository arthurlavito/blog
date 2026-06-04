
    <div class="max-w-7xl mx-auto px-4 text-center">
    <footer class="bg-white border-t border-gray-100 py-12 mt-20">    <h2 class="text-2xl font-black text-[#4B0082] mb-4">ANIM24<span class="text-indigo-500">.COM</span></h2>
        <p class="text-gray-400 text-sm max-w-md mx-auto mb-8">Providing the latest News,Articles, updates and insights from the world of and beyond.</p>
        <div class="flex justify-center gap-6 mb-8 text-sm font-bold text-gray-600">
            <a href="{{ route('about') }}" class="hover:text-[#4B0082]">About</a>
            <a href="{{ route('privacy-policy') }}" class="hover:text-[#4B0082]">Privacy</a>
            <a href="{{ route('contact') }}" class="hover:text-[#4B0082]">Contact</a>
        </div>
        <p class="text-xs text-gray-300 font-medium italic">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</footer> 
