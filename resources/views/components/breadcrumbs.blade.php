@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap items-center gap-1.5 text-[11px] font-black uppercase tracking-widest text-gray-400">
        @foreach($items as $item)
            @if(!$loop->last)
                <li>
                    <a href="{{ $item['url'] }}" class="hover:text-[#4B0082] transition-colors">
                        {{ $item['label'] }}
                    </a>
                </li>
                <li aria-hidden="true" class="text-gray-300 select-none">/</li>
            @else
                <li class="text-gray-700 truncate max-w-[20rem]" aria-current="page">
                    {{ $item['label'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>

@php
$schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => collect($items)
        ->map(fn ($item, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['label'],
            'item'     => $item['url'] ?? url()->current(),
        ])
        ->values()
        ->all(),
];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
