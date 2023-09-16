# Laravel API CRUD Generator


[![Latest Version on Packagist](https://img.shields.io/packagist/v/cookie/simple-crud-generator.svg?style=flat-square)](https://packagist.org/packages/cookie/simple-crud-generator)

The Laravel CRUD Generator is a command-line tool designed to streamline the process of creating CRUD (Create, Read, Update, Delete) operations for your Laravel-based RESTful API. It automates the generation of actions, routes, and resources, allowing you to quickly set up endpoints for managing your application's data models.

## Installation

You can install the package via composer:

```bash
composer require mrcookie/simple-api-crud-generator
```



## Usage

```php
php artisan api-crud:generate [App/Models/User or User or user]
```
and then you will get this routes with the related actions so you can customize everything to your needs:

```php
Route::name('users.')->prefix('users')->group(function () {
    Route::get('', App\Api\Actions\Users\GetUsersAction::class);
    Route::get('{id}', App\Api\Actions\Users\ShowUserAction::class);
    Route::put('{id}', App\Api\Actions\Users\UpdateUserAction::class);
    Route::delete('{id}', App\Api\Actions\Users\DeleteUserAction::class);
});
```


Im using `"spatie/laravel-query-builder": "^5.3"` to handle query and filtering. u can see `"spatie/laravel-query-builder": "^5.3"` [https://spatie.be/docs/laravel-query-builder/v5/introduction](documentation)


You can specified `allowedFilters` and `allowedFields` in your model

Example
```php
class User extends Model {
    public static array $allowedFilters = [
        'name'
    ];
    
    public static array $allowedFields = [
        'name'
    ];
}
```

## Notes

- this Package Uses [Laravel Actions](https://laravelactions.com) for the crud operations


- this Package Uses [Scramble](https://scramble.dedoc.co/usage/getting-started) To Generate API Documentation

Scramble Docs
```php
visit [http://localhost:8000/docs/api] to see the generated docs api routes
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [oOMrCookieOo](https://github.com/oOMrCookieOo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
