<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/api/mangadex', 'MangaProxy::api');
$routes->get('/api/test', 'MangaProxy::test');
$routes->get('/test', 'AuthController::test');
$routes->get('/covers/(:segment)/(:segment)', 'MangaProxy::cover/$1/$2');

$routes->get('/', 'Home::index');
$routes->post('login', 'AuthController::login');
$routes->post('register', 'AuthController::register');
$routes->post('forgot-password', 'AuthController::forgotPassword');
$routes->post('update-profile', 'AuthController::updateProfile');

$routes->options('(:any)', static function () {
    return service('response')->setStatusCode(200);
});