<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'BlogController::index');

// Auth
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attempt');
$routes->get('/logout', 'AuthController::logout');

// Public Blog
$routes->get('/blog', 'BlogController::index');
$routes->get('/post/(:segment)', 'BlogController::show/$1');

// Admin routes (protected)
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('posts', 'Admin\PostsController::index');
    $routes->get('posts/create', 'Admin\PostsController::create');
    $routes->post('posts', 'Admin\PostsController::store');
    $routes->get('posts/(:num)/edit', 'Admin\PostsController::edit/$1');
    $routes->post('posts/(:num)/update', 'Admin\PostsController::update/$1');
    $routes->get('posts/(:num)/delete', 'Admin\PostsController::delete/$1');
    $routes->get('posts/fetch-image', 'Admin\PostsController::fetchImage');
});

// API
$routes->get('/api/posts', 'Api\PostsApi::index');
$routes->get('/api/posts/(:segment)', 'Api\PostsApi::show/$1');
$routes->get('/api/dog/random', 'Api\DogApi::random');
