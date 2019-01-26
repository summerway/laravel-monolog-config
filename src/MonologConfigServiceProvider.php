<?php
/**
 * Created by PhpStorm.
 * User: Maple.xia
 * Date: 2019/1/22
 * Time: 4:01 PM
 */

namespace MapleSnow\MonologConfig;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Exception;

class MonologConfigServiceProvider extends ServiceProvider{

    public function register()
    {
        $configPath = realpath(__DIR__.'/config/logging.php');
        $this->mergeConfigFrom($configPath, 'logging');
    }

    /**
     * @throws Exception
     */
    public function boot()
    {
        $this->check();
        $this->publishConfig();
    }

    /**
     * @throws Exception
     */
    protected function check() {
        if(!$this->app instanceof LaravelApplication){
            throw new Exception("laravel application is off");
        }
    }

    protected function publishConfig() {
        $path = realpath(__DIR__.'/config/logging.php');
        $this->publishes([$path => config_path('logging.php')]);
    }
}