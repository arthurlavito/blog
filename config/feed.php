<?php

return [
    'feeds' => [
        'main' => [
            'items'       => [\App\Models\Post::class, 'getFeedItems'],
            'url'         => '/feed.xml',
            'title'       => 'Anim24 — Breaking Global News & Analysis',
            'description' => 'Latest news, anime, sports and global analysis from Anim24.',
            'language'    => 'en-US',
            'image'       => '',
            'format'      => 'rss',
            'view'        => 'feed::rss',
            'type'        => 'application/rss+xml',
            'contentType' => 'application/rss+xml;charset=UTF-8',
        ],
    ],
];
