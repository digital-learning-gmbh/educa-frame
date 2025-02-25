<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Grid Configuration
    |--------------------------------------------------------------------------
    |
    | Define the layout of widgets for the grid. Each row contains an array of
    | widgets, and each widget has a type ('local' or 'remote') and a size.
    | Remote components should specify a 'url' key.
    |
    | Pages allow for defining multiple layouts, each with its own grid setup.
    |
    */

    'pages' => [
        [
            'display_name' => 'Startseite',
            'key' => 'dashboard',
            'layout' => [
                [
                    // Row 1
                    [
                        'type' => 'local',
                        'component' => 'ClassbookMarkWidget',
                        'size' => 6, // Bootstrap column size
                    ],
                    [
                        'type' => 'local',
                        'component' => 'ClassbookExamList',
                        'size' => 6,
                    ],
                ],
                [
                    // Row 2
                    [
                        'type' => 'url',
                        'url' => 'https://example.com/widgets/ClassbookAbsenteeism',
                        'size' => 12,
                        'height' => "70vh"
                    ],
                    [
                        'type' => 'customComponent',
                        'url' => 'http://127.0.0.1:8000/main.js',
                        'size' => 6,
                    ],
                ],
            ],
        ],

        [
            'display_name' => 'Berichte',
            'key' => 'reports',
            'layout' => [
                [
                    // Row 2
                    [
                        'type' => 'url',
                        'url' => 'https://example.com/widgets/ReportDetails',
                        'size' => 12,
                    ],
                ],
                [
                    // Row 1
                    [
                        'type' => 'local',
                        'component' => 'ClassbookAbsenteeism',
                        'size' => 8,
                    ],
                    [
                        'type' => 'local',
                        'component' => 'ClassbookReport',
                        'size' => 4,
                    ],
                ],
            ],
        ],

        [
            'display_name' => 'Self-Service',
            'key' => 'settings',
            'layout' => [
                [
                    [
                        'type' => 'local',
                        'component' => 'ClassbookRIOSSample',
                        'size' => 12,
                    ],
                ],
            ],
        ],
    ],
];
