# Laravel Redirector

A simple module for managing your redirects. Can be useful on sites that are migrating from legacy routes or just have a
CMS that needs to manage the redirects.

## First steps

1. Install the package\
   `composer require ntpages/laravel-redirector`
2. Register service provider\
   `Ntpages\LaravelRedirector\Provider::class` in the `config/app.php`
3. Run the migrations\
   `php artisan migrate`
4. Publish package files\
   `php artisan vendor:publish`
5. Use the middleware, [official laravel docs](https://laravel.com/docs/8.x/middleware) about that.\
   `Ntpages\LaravelRedirector\RedirectMiddleware::class`

## Usage

todo
