<?php

namespace LBF\Devices;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use LBF\Devices\Models\Device;

class DevicesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->registerCustomValidationRules();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('LBF\Devices\Http\Controllers\DeviceController');
    }

    /**
     * Register our custom validation rules.
     */
    protected function registerCustomValidationRules()
    {
        Validator::extend('platform', function ($attribute, $value, $parameters, $validator) {
            return Device::isPlatformValueOrAlias($value);
        });
    }
}
