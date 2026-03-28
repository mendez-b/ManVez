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
//aqui se define la ruta para el login
$routes->post('login', 'AuthController::login');
//aqui se define la ruta para el registro de usuarios
$routes->post('register', 'AuthController::register');
//aqui se define la ruta para el reseteo de contraseña
$routes->post('forgot-password', 'AuthController::forgotPassword');
$routes->put('profile', 'AuthController::updateProfile');
$routes->match(['put', 'post'], 'profile', 'AuthController::updateProfile');

// Responder peticiones preflight (OPTIONS) para cualquier ruta
$routes->post('profile', 'AuthController::updateProfile');
$routes->options('(:any)', static function () {
    return service('response')->setStatusCode(200);
});

//POST: este indica que la ruta recibira datos (el email y la clave) de forma segura

//'login': esta sera la URL, si el servidor corre en el puerto 8080, 
//la URL completa para hacer login seria http://localhost:8080/login

//'AuthController::login': le dice a codelgniter que cuando alguien entre a /login,
//debe ejecutar el metodo "login" que esta dentro del controlador "AuthController.php"