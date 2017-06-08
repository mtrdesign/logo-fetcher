<?php

return [
    'general' => [
        'size'        => 512,
        'format'      => 'png',
        'upload_path' => 'logos' . DIRECTORY_SEPARATOR,
    ],

    'clearbit' => [
        'endpoint' => env('CLEARBIT_API_ENDPOINT', 'https://logo.clearbit.com/'),
    ],
];