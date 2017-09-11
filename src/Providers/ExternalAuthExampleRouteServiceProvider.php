<?php

namespace ExternalAuthExample\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

class ExternalAuthExampleRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router)
    {
        $router->get('login', 'ExternalAuthExample\Controllers\ContentController@showLoginPage');
        $router->post('token_signin', 'ExternalAuthExample\Controllers\AuthController@signInWithGoogleToken');
        $router->post('credentials_signin', 'ExternalAuthExample\Controllers\AuthController@signInWithCredentials');
        $router->post('connect_google', 'ExternalAuthExample\Controllers\AuthController@connectGoogleAccount');
        $router->post('logout', 'ExternalAuthExample\Controllers\AuthController@logout');
        $router->get('home', 'ExternalAuthExample\Controllers\ContentController@showUserHomePage');
    }
}