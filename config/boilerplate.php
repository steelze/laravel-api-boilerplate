<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FRONTEND RESET URL
    |--------------------------------------------------------------------------
    |
    | This value is the link to the rest url of your frontend application. This value is used when a
    | user requests link for resetting password. The reset token and email will be appended to this url.
    |
    */
    'frontend_reset_url' => env('FRONTEND_RESET_URL', 'http://localhost:3000/reset'),

    /*
    |--------------------------------------------------------------------------
    | FRONTEND HOME URL
    |--------------------------------------------------------------------------
    |
    | This value is the link to the home url of your frontend application. This value is used when
    | laravel needs to redirect to the frontend homepage, notably during verification.
    |
    */
    'frontend_home_url' => env('FRONTEND_HOME_URL', 'http://localhost:3000'),
];
