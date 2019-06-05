<?php
/**
 * Created by PhpStorm.
 * User: miliv
 * Date: 4/16/2019
 * Time: 4:37 PM
 */

namespace milivoje\backups;

use Illuminate\Support\ServiceProvider;

class UpgradeServiceProvider extends ServiceProvider
{

    public function boot()
    {

        dd('asdfasdf');

//        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
//        $this->loadMigrationsFrom(__DIR__.'/database');
//        $this->loadViewsFrom(__DIR__ . '/views', 'milivoje');
//        $this->registerHelpers();
//        $this->publishes([
//            __DIR__.'/views' => base_path('resources/views/wisdmlabs/todolist'),
//        ]);

//        $this->app->register(\milivoje\service\UpdatesServiceProvider::class);

//        $this->commands([
//            \milivoje\service\shellCommands\ShellCommand::class
//        ]);

    }


    public function register()
    {

//        $this->app['service'] = $this->app->share(function ($app) {
//            return new service;
//        });

    }
}
