<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/api/mangadex', 'MangaProxy::api');
$routes->get('/covers/(:segment)/(:segment)', 'MangaProxy::cover/$1/$2');

$routes->get('/', 'Home::index');
