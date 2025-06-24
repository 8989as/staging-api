<?php

namespace Webkul\PhoneAuth\Providers;

use Webkul\Core\Providers\ModuleServiceProvider;

class PhoneAuthServiceProvider extends ModuleServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'phoneauth');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'phoneauth');

        $this->publishes([
            __DIR__.'/../Config/phoneauth.php' => config_path('phoneauth.php'),
        ], 'config');

        // Register listeners for custom events
        \Illuminate\Support\Facades\Event::listen('phoneauth.otp.sent', [\Webkul\PhoneAuth\Listeners\SendOtpNotification::class, 'handle']);
        // Add more listeners as needed, or document for users how to register their own
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        // Bind Bagisto's Customer model to PhoneAuth's Customer model
        $this->app->bind(\Webkul\Customer\Models\Customer::class, \Webkul\PhoneAuth\Models\Customer::class);
        $this->app->bind(\Webkul\Customer\Contracts\Customer::class, \Webkul\PhoneAuth\Models\Customer::class);
        $this->app['router']->aliasMiddleware('phone.verified', \Webkul\PhoneAuth\Http\Middleware\PhoneVerified::class);
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/phoneauth.php', 'phoneauth'
        );
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }
}