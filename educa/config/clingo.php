<?php

return [

    'path_windows' => '\clingo\windows\clingo.exe',
    'path_linux' => DIRECTORY_SEPARATOR.'clingo'.DIRECTORY_SEPARATOR.'ubuntu'.DIRECTORY_SEPARATOR.'clingo',
    'path_mac' => DIRECTORY_SEPARATOR.'clingo'.DIRECTORY_SEPARATOR.'macos'.DIRECTORY_SEPARATOR.'clingo',

    'program' => [

        'timetable-1' => [
            'path' => DIRECTORY_SEPARATOR .'clingo'.DIRECTORY_SEPARATOR .'asp'.DIRECTORY_SEPARATOR.'timetable.lp',
            'name' => 'Timetable Version 1',
            'params' => '',
            'default' => true,
        ],

        'timetable-2' => [
            'path' => 'redis',
            'name' => 'default',
            'default' => false,
        ],
    ],

];
