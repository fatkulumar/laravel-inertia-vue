<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


[Laravel 11.x Inertia Vue](https://laravel.com/docs/10.x)

##### Libraries
- [Laravel Breeze And Inertia Vue](https://laravel.com/docs/11.x/starter-kits#breeze-and-inertia)
- [CKeditor 5](https://ckeditor.com/docs/ckeditor5/latest)
- [Flowbite 3.2.0](https://flowbite.com)
- [Sweetalert2 11.11.0](https://sweetalert2.github.io)
- [Indonesia Province](https://github.com/laravolt/indonesia)
- [Vue-QRCode](https://github.com/fengyuanchen/vue-qrcode)
- [Laravel Excel](https://docs.laravel-excel.com/)
- [Brevo Email](https://app.brevo.com/)

##### Requirements
- PHP 8.1 - 8.2

##### Installation
    ```
    composer install
    ```
    ```
    php artisan migrate:fresh --seed --seed
    ```
    ```
    copy .env-example to .env
    ```

##### Installation
     ```
    php artisan serve
    ```
##### Structure
- app
    - DataTransferObject
    - Http
    - Models
    - Providers
    - Repositories
    - Services
    - Traits
        - Acessor
- bootstrap
- config
- database
    - factories
    - migrations
    - seeders
- public
- resources
    - css
    - js
    - views
        - email
- routes
- storage
    - app
    - framework
    - logs
- tests

#### Source
[Learning Resources](https://github.com/yaza-putu/laravel-repository-with-service/tree/master/src)

#### Laravel Optimize Performance (optional)
1. When installing vendors in Laravel, use the --no-dev option so that development dependencies are not installed.
    ```
    composer install --optimize-autoloader --no-dev
    ```
2. Use artisan optimize
    ```
    php artisan optimize
    ```
