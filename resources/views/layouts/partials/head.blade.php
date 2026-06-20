<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', config('app.name', 'Anim24') . ' — Breaking Global News & Analysis')</title>

<!-- SEO: Description & Canonical -->
<meta name="description" content="@yield('meta_description', 'Anim24 — Breaking global news, anime, sports, and analysis.')">
<link rel="canonical" href="@yield('canonical', url()->current())">

<!-- Open Graph -->
<meta property="og:title" content="@yield('og_title', config('app.name', 'Anim24'))">
<meta property="og:description" content="@yield('meta_description', 'Anim24 — Breaking global news, anime, sports, and analysis.')">
<meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:url" content="@yield('canonical', url()->current())">
<meta property="og:site_name" content="{{ config('app.name', 'Anim24') }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('og_title', config('app.name', 'Anim24'))">
<meta name="twitter:description" content="@yield('meta_description', 'Anim24 — Breaking global news, anime, sports, and analysis.')">
<meta name="twitter:image" content="@yield('og_image', asset('images/logo.png'))">

<!-- Google Search Console Verification -->
@if(config('services.google.site_verification'))
<meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
@endif

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<!-- Scripts -->
@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Google Analytics (GA4) -->
@if(config('services.google.analytics_id'))
@php $gaId = config('services.google.analytics_id'); @endphp
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $gaId }}');
</script>
@endif

<!-- Google AdSense -->
@if(config('services.google.adsense_client_id'))
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('services.google.adsense_client_id') }}"
        crossorigin="anonymous"></script>
@endif

<!-- Organization + WebSite Schema -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@graph": [
    {
      "@@type": "Organization",
      "@@id": "{{ config('app.url') }}/#organization",
      "name": "Anim24",
      "url": "{{ config('app.url') }}",
      "logo": {
        "@@type": "ImageObject",
        "url": "{{ asset('images/logo.png') }}"
      }
    },
    {
      "@@type": "WebSite",
      "@@id": "{{ config('app.url') }}/#website",
      "url": "{{ config('app.url') }}",
      "name": "Anim24",
      "publisher": { "@@id": "{{ config('app.url') }}/#organization" },
      "potentialAction": {
        "@@type": "SearchAction",
        "target": {
          "@@type": "EntryPoint",
          "urlTemplate": "{{ route('posts.index') }}?search={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>
