## Laravel API Boilerplate Laravel 6

Laravel API Boilerplate is a starter kit you can use to build your API ontop. This project is built on Laravel 6!

It is built on top of three big guys:

* JWT Auth - [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
* Laravel CORS [barryvdh/laravel-cors](http://github.com/barryvdh/laravel-cors)

## Application Features
* User can register and login
* User can verify email
* User can update profile


## API Endpoints
Method | Route | Description
--- | --- | ---
`POST` | `/api/register` | Create a user
`POST` | `/api/login` | Login an already registered user
`GET` | `/api/me` | Get authenticated user
`POST` | `/api/password/email` | Send password reset email
`POST` | `/api/password/reset` | Reset user password
`POST` | `/api/logout` | Logout authenticated user
`POST` | `/api/email/resend` | Resend verification email

## Setup
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

  - Open terminal and run the following commands
    ```
    $ git clone https://github.com/Steelze/laravel-api-boilerplate.git
    $ cd laravel-api-boilerplate
    $ composer install
    $ php artisan key:generate
    $ php artisan jwt:secret
    ```
  - Duplicate and save .env.example as .env and fill in environment variables
    ```
    $ php artisan migrate
    ```
  ### Run The Service
  ```
  $ php artisan serve
  ```

## Configuration

All boilerplate specific settings can be found in the `config/boilerplate.php` config file.

```php
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

```
## Cross Origin Resource Sharing
This project uses _barryvdh/laravel-cors_ package, to handle CORS easily. Read <a href="https://github.com/barryvdh/laravel-cors" target="_blank">the docs</a> for more info.

## Testing
  ```
  $ vendor/bin/phpunit
  ```
If correctly setup, all tests should pass

## Feedback

I created this project for personal purpose and for anyone who might need this. I'd appreciate feedback regarding this project!

## Author
Odunayo Ileri Ogungbure

## License
MIT
