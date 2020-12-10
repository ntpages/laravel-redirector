<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | This package is made for projects that need handle redirects dynamically
    | that's why we don't use the configuration file as a storage for redirects
    | if your app can be using a static file consider redirecting using web server
    | (apache or nginx).
    |
    | You can specify the model of you system user in the auditable property.
    | `'auditable' => App\Model\User::class`
    |
    | That'll build the relation in the database and automatically will store
    | the information about a creator/editor of the redirect.
    |
    */

    'database' => [
        'table' => 'redirects',
        'auditable' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | 99% of the cases should always use cache, it's much faster.
    | For the 1% those that have over 200k of redirects disable by `cache` => false
    | Default one day.
    |
    */

    'cache' => [
        'key' => 'redirector',
        'ttl' => 86400,
    ]
];
