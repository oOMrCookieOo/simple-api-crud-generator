<?php

namespace MrCookie\SimpleApiCrudGenerator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MrCookie\SimpleApiCrudGenerator\Commands\SimpleApiCrudGeneratorCommand;

class SimpleApiCrudGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('simple-api-crud-generator')
            ->hasCommand(SimpleApiCrudGeneratorCommand::class);
    }
}
