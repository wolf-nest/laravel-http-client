<?php

/*
 * This file is part of the tlwl/http-client.
 * 
 * (c) 悟玄 <roc9574@sina.com>
 * 
 * This source file is subject to the MIT license that is bundled.
 * with this source code in the file LICENSE.
 */

namespace Tlwl\HttpClient;

use HttpClient;
use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider{

    public function boot(){
        $this->publishes([
            __DIR__.'/../config/http-client.php' => config_path('http-client.php'),
        ], 'config');
        $this->mergeConfigFrom(__DIR__.'/../config/http-client.php', 'http-client');

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/http-client.php', 'http-client');

        $this->app->singleton('http-client', function () {
            return new HttpClient(config('http-client'));
        });

        $this->app->alias(HttpClient::class, 'http-client');
    }

    /**
     * Get services.
     * @author 杨鹏 <yangpeng1@dgg.net>
     * @return array
     */
    public function provides()
    {
        return ['http-client'];
    }
}