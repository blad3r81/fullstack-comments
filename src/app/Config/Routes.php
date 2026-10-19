<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Comments::index');
$routes->get('comments', 'Comments::list');
$routes->post('comments', 'Comments::create');
$routes->post('comments/(:num)/delete', 'Comments::delete/$1');
