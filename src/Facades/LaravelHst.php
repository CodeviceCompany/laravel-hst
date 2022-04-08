<?php

namespace CodeviceCompany\LaravelHst\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CodeviceCompany\LaravelHst\LaravelHst
 */
class LaravelHst extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-hst';
    }
}
