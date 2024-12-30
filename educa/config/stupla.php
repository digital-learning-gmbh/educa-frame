<?php

return [
    'cors' => '*.academy-languages.de *.fuu.de ',
    'allow_from' => 'https://preview.academy-languages.de/',
    'search' => [
      'models' => [
          \App\Schuler::class,
          \App\Raum::class,
          \App\Lehrer::class,
          \App\Group::class,
          \App\Task::class,
          \App\Beitrag::class,
          \App\Appointment::class,
          \App\CloudID::class
      ]
    ],

    'documents' => [
        'onlyoffice' => [
            'server' => 'https://office001.educacloud.de/',
            'isDocker' => false,
        ]
    ],

    "system" => "test",

    'useDraft' => false,

    'provider' => [
        'schulerprovider' => null,
        'dozentenprovider' => null
    ],

    'browserless' =>
    [
       'url' => 'http://print.educa-portal.de:4500',
        'token' => '?token=educaeduca321'
    ],

    'aufgaben' => [
        'watcher' => [
            \App\Http\Controllers\Aufgaben\Example\GenerateDozentenTokenAufgabe::class,
		  \App\Http\Controllers\Aufgaben\Example\SinnloseAufgabe::class,
            \App\Http\Controllers\Aufgaben\Example\MissingEntryKlassenbuchAufgabe::class,
            \App\Http\Controllers\Aufgaben\Example\FehlzeitenMaxAufgabe::class,


            \App\Http\Controllers\Aufgaben\Example\EmailAufgabe::class,
            \App\Http\Controllers\Aufgaben\Customer\ImportSVSPlanAufgabe::class
        ]
    ],

    'preiskalkulator' => [
        'active' => false
    ],

    'einstufungstest' => [
        'active' => 'true'
    ],

    'dozenten' => [
        'active' => true
    ],

    'unternehmen' => [
        'active' => false,
        'emailTemplate' => [
            'default' => 'emails.welcomePraxis',
            1 => 'emails.customer.welcomePraxisDA',
            2 => 'emails.customer.welcomePraxisHD'
        ],
        'useCompanyData' => true,
    ],

    'cloud' => [
        'active' => true
    ],

    'bugreporting' => [
        'active' => true,
        'supportEmail' => 'edsupport@ibadual.com',
        'errorEmail' => 'edsupport@ibadual.com',
    ],

    'classbook' => [
         'useRemember' => true,
    ],

    'timetable' => [
        'print'=> [
            'customHeader' => 'stundenplan.snippets.header',
            'customFooter' => 'stundenplan.snippets.footer'
        ],
    ],



];
