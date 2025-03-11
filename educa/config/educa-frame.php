<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pages Configuration
    |--------------------------------------------------------------------------
    |
    | Each entry in "pages" represents a page (or tab) in your application.
    | The "layout" key defines an array of rows, where each row is an array
    | of widget definitions.
    |
    | Widgets can have:
    |   - 'type': "local", "url", or "customComponent"
    |   - 'component': Name of local component (if type="local")
    |   - 'url': URL for iframes or remote components (if type="url" or type="customComponent")
    |   - 'size': Fallback bootstrap column size (e.g. 6 => col-6)
    |   - 'height': Optional iframe height if needed
    |   - 'responsive': Optional array of breakpoints (e.g. ['xs' => 12, 'md' => 6, 'lg' => 4])
    */

    'pages' => [
        [
            'display_name' => 'Startseite',
            'key'          => 'dashboard',
            'layout'       => [
                [
                    // Row 1
                    [
                        'type'      => 'local',
                        'component' => 'ClassbookMarkWidget',
                        'size'      => 12,
                        // Optional responsive sizing
                        'responsive' => [
                            'xs' => 12,
                            'md' => 12,
                            'lg' => 6,
                        ],
                    ],
                    [
                        'type'      => 'local',
                        'component' => 'ClassbookExamList',
                        'size'      => 12,
                        'responsive' => [
                            'xs' => 12,
                            'md' => 12,
                            'lg' => 6,
                        ],
                    ],
                ],
                [
                    // Row 2
                    [
                        'type'   => 'url',
                        'url'    => 'https://example.com/widgets/ClassbookAbsenteeism',
                        'size'   => 12,
                        'height' => '70vh',
                        // Optional responsive sizing
                        'responsive' => [
                            'xs' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ],
                    ],
                    [
                        'type' => 'customComponent',
                        'url'  => 'http://127.0.0.1:8000/main.js',
                        'size' => 6,
                        // No responsive data, so it remains col-6 for all breakpoints
                    ],
                ],
            ],
        ],

        [
            'display_name' => 'Berichte',
            'key'          => 'reports',
            'layout'       => [
                [
                    // Row 1
                    [
                        'type' => 'url',
                        'url'  => 'https://example.com/widgets/ReportDetails',
                        'size' => 12,
                    ],
                ],
                [
                    // Row 2
                    [
                        'type'      => 'local',
                        'component' => 'ClassbookAbsenteeism',
                        'size'      => 8,
                        'responsive' => [
                            'xs' => 12,
                            'sm' => 6,
                            'md' => 8,
                            // could also add 'lg' => 8, etc.
                        ],
                    ],
                    [
                        'type'      => 'local',
                        'component' => 'ClassbookReport',
                        'size'      => 4,
                    ],
                ],
            ],
        ],

        [
            'display_name' => 'Self-Service',
            'key'          => 'settings',
            'layout'       => [
                [
                    // Single row with single widget
                    [
                        'type'      => 'local',
                        'component' => 'ClassbookRIOSSample',
                        'size'      => 12,
                    ],
                ],
            ],
        ],
    ],
];
