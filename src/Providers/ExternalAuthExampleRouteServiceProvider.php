<?php

namespace ExternalAuthExample\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

class ExternalAuthExampleRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router)
    {
        $router->get('login', 'ExternalAuthExample\Controllers\ContentController@showLoginPage');
        $router->post('tokensignin', 'ExternalAuthExample\Controllers\AuthController@signInWithToken');
    }
}