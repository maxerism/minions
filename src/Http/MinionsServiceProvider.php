<?php

namespace Minions\Http;

use Illuminate\Console\Application as Artisan;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Orchestra\Canvas\Core\Presets\Laravel;
use Illuminate\Filesystem\Filesystem;

class MinionsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minions.router', static function (Container $app) {
            return new Router($app, $app->make('minions.config'));
        });

        $this->app->bind('minions.controller', static function (Container $app) {
            return new Controller($app, $app->make('minions.router'));
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootRpcRoutes();

        $this->publishes([
            __DIR__.'/stubs/route.stub' => \base_path('routes/rpc.php'),
        ], 'routes');

        if ($this->app->runningInConsole()) {
            $preset = new Laravel($this->app);
            $filesystem = $this->app->make(Filesystem::class);
            Artisan::starting(function ($artisan) use ($filesystem) {
                $artisan->add(new Console\MakeRpcRequest($filesystem));
            });
        }
    }

    /**
     * Register rpc routes.
     *
     * @return void
     */
    protected function bootRpcRoutes()
    {
        $routeFile = $this->app->basePath('routes/rpc.php');

        if (\file_exists($routeFile)) {
            Router::routeResolver(static function () use ($routeFile) {
                require $routeFile;
            });
        }
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function provides()
    {
        return ['minions.controller', 'minions.router'];
    }
}
