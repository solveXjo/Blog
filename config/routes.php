<?php

require "app/core/Router.php";
$router = new Router();

$router->addRoute('/',   'app/views/index.view.php');
$router->addRoute('/home',   'app/views/index.view.php');
$router->addRoute('/index', __DIR__ . '/../app/views/index.view.php');
$router->addRoute('/signup', __DIR__ . '/../app/views/auth/signup.view.php');
$router->addRoute('/posts', __DIR__ . '/../app/views/posts.view.php');
$router->addRoute('/profile', __DIR__ . '/../app/views/profile.view.php');
$router->addRoute('/Login', __DIR__ . '/../app/views/auth/Login.view.php');
$router->addRoute('/about', __DIR__ . '/../app/views/about.view.php');
$router->addRoute('/contact', __DIR__ . '/../app/views/contact.view.php');
$router->addRoute('/profile_edit', __DIR__ . '/../app/views/profile_edit.view.php');
$router->addRoute('/category', __DIR__ . '/../app/views/category.view.php');

$router->addDynamicRoute('/category/', __DIR__ . '/../app/views/category.view.php', 'category');
$router->addDynamicRoute('/comment/', __DIR__ . '/../app/views/comment.view.php', 'post_id');

return $router;
