<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen">

    {{-- Navigation --}}
    @include('layouts.partials.navbar')

    {{-- Optional Page Header --}}
    <section class="bg-white border-b">
        @include('layouts.categories-header')
    </section>

   

    {{-- Page Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

</body>
</html>
