<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/core/Router.php';

$router = new App\Core\Router();

// Test basic route
$router->addRoute('/test', __DIR__ . '/resources/views/test.view.php');
echo "Testing basic route...\n";
$router->route('/test');

// Test dynamic route
$router->addDynamicRoute('/category/', __DIR__ . '/resources/views/category.view.php', 'category');
echo "\nTesting dynamic route...\n";
$router->route('/category/technology');

// Test non-existent route
echo "\nTesting fallback route...\n";
$router->route('/non-existent-route');
