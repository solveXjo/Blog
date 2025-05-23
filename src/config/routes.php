<?php

use App\Controllers\AboutController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\CommentController;
use App\Controllers\ContactController;
use App\Controllers\EditProfileController;
use App\Controllers\ErrorController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Controllers\ProfileController;
use App\Controllers\SinglePostController;
use App\Core\Router;
use App\Core\App;


return function(App $app) {
    $router = $app->router;


// var_dump(HomeController::class);
    $router->addRoute('GET', '/', HomeController::class, 'show');
    $router->addRoute('GET', '/index', HomeController::class, 'handle');
    $router->addRoute('GET', '/home', HomeController::class, 'show');
    $router->addRoute('POST', '/home', HomeController::class, 'handlePostRequest');


    $router->addRoute('GET', '/signup', AuthController::class, 'showSignup');
    $router->addRoute('POST', '/signup', AuthController::class, 'showSignup');
    $router->addRoute('GET', '/Login', AuthController::class, 'showLogin');
    $router->addRoute('POST', '/Login', AuthController::class, 'showLogin');


    $router->addRoute('GET', '/about', AboutController::class, 'show');


    $router->addRoute('GET', '/posts', PostController::class, 'show');
    $router->addRoute('POST', '/posts', PostController::class, 'show');

    $router->addRoute('GET', '/category', CategoryController::class, 'show');
    $router->addRoute('POST', '/category', CategoryController::class, 'show');


    $router->addRoute('GET', '/profile', ProfileController::class, 'show');
    $router->addRoute('POST', '/profile', ProfileController::class, 'show');

    $router->addRoute('GET', '/profile_edit', EditProfileController::class, 'show');
    $router->addRoute('POST', '/profile_edit', EditProfileController::class, 'show');

    $router->addRoute('GET', '/contact', ContactController::class, 'show');
    $router->addRoute('POST', '/contact', ContactController::class, 'show');



    $router->addDynamicRoute('GET', '/post/', SinglePostController::class, 'show', 'id');

    $router->addDynamicRoute('POST', '/post/', SinglePostController::class, 'show', 'id');


    $router->addIdSlugRoute('GET', '/post/', SinglePostController::class, 'show');

    $router->addDynamicRoute('POST', '/post/', SinglePostController::class, 'show', 'id');

    $router->addDynamicRoute('GET', '/category/', CategoryController::class, 'show', 'category');
    $router->addDynamicRoute('POST', '/category/', CategoryController::class, 'show', 'category');


    $router->addDynamicRoute('GET', '/comment/', CommentController::class, 'show', 'post_id');
    $router->addDynamicRoute('POST', '/comment/', CommentController::class, 'show', 'post_id');
    return $router;
};
