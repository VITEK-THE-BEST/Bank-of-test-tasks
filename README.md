# CreaTest 
for Ugra State University



## run project
requirements: 
- PHP 8.1
- Laravel 9.1
- MySQL 8^


### install backpack for admins
- php artisan backpack:install
- php artisan key:generate

### rebase project
- php artisan migrate:fresh --seed

### update documentation
- php artisan scribe:generate

 you can see [documentation](https://app.swaggerhub.com/apis/VITEK-THE-BEST/CreaTest)
# usage libs:

- [sanctum](https://laravel.com/docs/9.x/sanctum)
- [Laravel-Backpack/PermissionManager](https://github.com/Laravel-Backpack/PermissionManager)
- [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
- [knuckleswtf/scribe](https://github.com/knuckleswtf/scribe)
