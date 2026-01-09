<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Anim24') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50/50">
            {{-- Logo Area --}}
            <div class="mb-8">
                <a href="/">
                    <h1 class="text-5xl font-black tracking-tighter text-[#4B0082]">
                        ANIM<span class="text-indigo-500">24</span>
                    </h1>
                </a>
            </div>

            {{-- The Form Card --}}
            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-sm border border-gray-100 rounded-[2.5rem] overflow-hidden">
                {{ $slot }}
            </div>
            
            <p class="mt-6 text-xs font-bold text-gray-400 uppercase tracking-widest">© 2026 Anim24 News Network</p>
        </div>
    </body>
</html>