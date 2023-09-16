<?php

namespace MrCookie\SimpleApiCrudGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MrCookie\SimpleApiCrudGenerator\SimpleApiCrudGenerator
 */
class SimpleApiCrudGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MrCookie\SimpleApiCrudGenerator\SimpleApiCrudGenerator::class;
    }
}
