<?php

require "app/core/Router.php";
$router = new Router();

$router->addRoute('/',   'resources/views/index.view.php');
$router->addRoute('/index', __DIR__ . '/../resources/views/index.view.php');
$router->addRoute('/signup', __DIR__ . '/../resources/views/auth/signup.view.php');
$router->addRoute('/posts', __DIR__ . '/../resources/views/posts.view.php');
$router->addRoute('/profile', __DIR__ . '/../resources/views/profile.view.php');
$router->addRoute('/Login', __DIR__ . '/../resources/views/auth/Login.view.php');
$router->addRoute('/about', __DIR__ . '/../resources/views/about.view.php');
$router->addRoute('/contact', __DIR__ . '/../resources/views/contact.view.php');
$router->addRoute('/profile_edit', __DIR__ . '/../resources/views/profile_edit.view.php');
$router->addRoute('/category', __DIR__ . '/../resources/views/category.view.php');

$router->addDynamicRoute('/category/', __DIR__ . '/../resources/views/category.view.php', 'category');
$router->addDynamicRoute('/comment/', __DIR__ . '/../resources/views/comment.view.php', 'post_id');

return $router;
