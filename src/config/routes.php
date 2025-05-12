<?php

use App\Core\Router;

$router = new Router();

// Static routes
$router->addRoute('GET', '/', 'HomeController', 'show');
$router->addRoute('GET', '/home', 'HomeController', 'show');
$router->addRoute('GET', '/index', 'HomeController', 'handle');
$router->addRoute('GET', '/signup', 'AuthController', 'signup');
$router->addRoute('POST', '/signup', 'AuthController', 'handleSignup');
$router->addRoute('GET', '/posts', 'PostController', 'show');

$router->addRoute('GET', '/profile', 'ProfileController', 'show');
$router->addRoute('GET', '/login', 'AuthController', 'login');
$router->addRoute('POST', '/login', 'AuthController', 'handleLogin');
$router->addRoute('GET', '/about', 'AboutController', 'show');
$router->addRoute('GET', '/contact', 'ContactController', 'show');
$router->addRoute('GET', '/profile_edit', 'EditProfileController', 'show');
$router->addRoute('POST', '/profile_edit', 'ProfileController', 'update');
$router->addRoute('GET', '/category', 'CategoryController', 'show');

// Dynamic routes
$router->addDynamicRoute('GET', '/category/', 'CategoryController', 'show', 'category');
$router->addDynamicRoute('GET', '/post/', 'SinglePostController', 'show', 'post_id');

$router->addDynamicRoute('GET', '/comment/', 'CommentController', 'show', 'post_id');
$router->addDynamicRoute('POST', '/comment/', 'CommentController', 'show', 'post_id');
return $router;
