<?php

return [
    'languages' => ["de", "en", "fr", "pl", "ru", "cs", "sk", "tr", "hu","uk"],
    'showLanguageSelectNavbar' => true,

    'learnLib' => [
        'showExplore' => true,
        'showSearch' => true,
        'showBrowser' => true,
        'showCreatedByMe' => true,
        'showFavorites' => true,
        'showInteractiveCourses' => true,

        'explorer' => [
            'showCategories' => true,
            'showLastAdd' => true,
        ],

        'menu' => [
            'showCategories' => true,
            'showBookmarks' => true,
            'showHighlight' => true,
            'showProvider' => true,
        ],

        'defaultAddToLearnBib' => true,
    ],

    'self_service' => [
        'enabled' => true,
        'domain' => 'get.edunex.de'
    ],

    'banner' => [
        'show' => false,
        'color' => '#0D47A1',
        'text' => "educa ist im Testmodus gestartet."
    ],

    'ai' => [
        'backend' => 'https://api.educaai.de/',
        'token' => ''
    ],

    'push' => [
        'email' => true,
        'overrideEmail' => env("OVERRIDE_EMAIL",false),
    ],

    'h5p' => [
        'backend' => [
            'base' => "https://h5p-hub.educacloud.de/api/v1/",
        ]
    ],

    'search' => [
        'fullSearch' => false
    ],

    'encrypt' => env("ENABLE_E2E",false)
];
