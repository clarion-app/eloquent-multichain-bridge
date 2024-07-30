<?php

namespace ClarionApp\EloquentMultiChainBridge;

use Illuminate\Support\ServiceProvider;
use ClarionApp\EloquentMultiChainBridge\Commands\RegisterDataStream;
use ClarionApp\EloquentMultiChainBridge\Commands\SyncStreamFromChain;
use ClarionApp\EloquentMultiChainBridge\Commands\SyncChainFromModels;
use ClarionApp\EloquentMultiChainBridge\Commands\NewBlock;

class EloquentMultiChainProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            RegisterDataStream::class,
            SyncStreamFromChain::class,
            SyncChainFromModels::class,
            NewBlock::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        if(!$this->app->routesAreCached())
        {
            require __DIR__.'/Routes.php';
        }

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('eloquent-multichain-bridge.php'),
        ], 'emc-config');
    }
}

