<?php
/**
 * Created by PhpStorm.
 * User: miliv
 * Date: 4/16/2019
 * Time: 4:37 PM
 */

namespace ItVision\ServerBackup;

use Illuminate\Support\ServiceProvider;
use ItVision\ServerBackup\console\InitBackupLimit;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class BackupServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database');
        $this->loadViewsFrom(__DIR__ . '/views', 'backup');

//        $this->registerHelpers();
//        $this->publishes([
//            __DIR__.'/views' => base_path('resources/views/wisdmlabs/todolist'),
//        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InitBackupLimit::class,
            ]);
        }

    }


    public function register()
    {

//        $this->app['service'] = $this->app->share(function ($app) {
//            return new service;
//        });

    }
}
