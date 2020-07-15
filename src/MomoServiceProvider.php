<?php

namespace Momo\SDK;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class MomoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('momo.client', function ($app) {
            $options = $app['config']->get('momo');

            if (!isset($options['api_url'])) {
                throw new \InvalidArgumentException('Not found api_url config');
            }

            if (!isset($options['oauth']['url'])) {
                throw new \InvalidArgumentException('Not found oauth.url config');
            }

            if (!isset($options['oauth']['client_id'])) {
                throw new \InvalidArgumentException('Not found oauth.client_id config');
            }

            if (!isset($options['oauth']['client_secret'])) {
                throw new \InvalidArgumentException('Not found oauth.client_secret config');
            }

            return new MomoClient($options['api_url']);
        });
    }

    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('momo.php')], 'momo');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('momo');
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/momo.php';
    }
}