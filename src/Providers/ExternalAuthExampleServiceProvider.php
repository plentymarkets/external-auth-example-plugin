<?php

namespace ExternalAuthExample\Providers;

use Plenty\Plugin\ServiceProvider;

class ExternalAuthExampleServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     */

    public function register()
    {
        $this->getApplication()->register(ExternalAuthExampleRouteServiceProvider::class);
    }
}