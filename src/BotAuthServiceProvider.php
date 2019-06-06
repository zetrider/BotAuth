<?php

namespace ZetRider\BotAuth;

use ZetRider\BotAuth\BotAuthManager;

use Illuminate\Support\ServiceProvider;

class BotAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('BotAuth', function ($app) {
            return new BotAuthManager($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // php artisan vendor:publish --tag=botauth-config
        $this->publishes([
            __DIR__.'/../config/botauth.php' => config_path('botauth.php'),
        ], 'botauth-config');

        // php artisan vendor:publish --tag=botauth-migrations
        $this->publishes([
            __DIR__.'/Database/migrations/' => database_path('migrations')
        ], 'botauth-migrations');

        // php artisan vendor:publish --tag=botauth-views
        $this->publishes([
            __DIR__.'/Resources/views' => resource_path('views/vendor/botauth'),
        ], 'botauth-views');

        // php artisan vendor:publish --tag=botauth-translations
        $this->publishes([
            __DIR__.'/Translations' => resource_path('lang/vendor/botauth'),
        ], 'botauth-translations');

        $this->loadTranslationsFrom(__DIR__.'/Translations', 'botauth');

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $this->loadViewsFrom(__DIR__.'/Resources/views', 'botauth');

        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
    }

}
