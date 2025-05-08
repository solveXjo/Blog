<?php

use App\Controllers\HomeController;

require "src/app/core/Router.php";
$router = new Router();

$router->addRoute('/',   'src/app/views/index.view.php');
$router->addRoute('/home',   'src/app/views/index.view.php');
$router->addRoute('/index',   'src/app/views/index.view.php');
$router->addRoute('/signup', 'src/app/views/auth/signup.view.php');
$router->addRoute('/posts', 'src/app/views/posts.view.php');
$router->addRoute('/profile', 'src/app/views/profile.view.php');
$router->addRoute('/Login', 'src/app/views/auth/Login.view.php');
$router->addRoute('/about',  'src/app/views/about.view.php');
$router->addRoute('/contact', 'src/app/views/contact.view.php');
$router->addRoute('/profile_edit', 'src/app/views/profile_edit.view.php');
$router->addRoute('/category', 'src/app/views/category.view.php');

$router->addDynamicRoute('/category/', 'src/app/views/category.view.php', 'category');
$router->addDynamicRoute('/comment/', 'src/app/views/comment.view.php', 'post_id');


// $router->addRoute('/home', HomeController::class, 'home');

// $routes = [
//     '' => 'x/y/z/site/index',
//     'login' => 'site/login',
//     'register' => 'site/register',

// ];

return $router;
