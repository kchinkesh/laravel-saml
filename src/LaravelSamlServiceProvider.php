<?php

namespace Kchinkesh\LaravelSaml;

use Illuminate\Support\ServiceProvider;
use Kchinkesh\LaravelSaml\Auth\SamlAuth;

class LaravelSamlServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../routes/web.php';

        $this->publishes([
            __DIR__ . '/../config/samlidp_settings.php' => config_path('samlidp_settings.php'),
        ], 'saml-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SamlAuth::class, function () {
            $auth = SamlAuth::loadOneLoginAuthFromIpdConfig(config('samlidp_settings.idp.name'));
            return new SamlAuth($auth);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SamlAuth::class];
    }
}
