<?php
return [
    "path" => base_path() . "/app/Modules",
    "base_namespace" => "App\Modules",

    'modules'   => [
        'Admin' => [
            'Auth', 'Users'
        ],
        'Blog'  => [
            'Posts',
            'News' => [
                'Posts'
            ],
            'fims' => [
                'posd'
            ],
            'Fims' => [
                'posd'
            ],
            'Fimss' => [
                'possd'
            ]
        ],
        'Shop'
    ],
];
