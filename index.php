<?php

require_once 'app/models/PostRepository.php';
require_once 'app/core/Database.php';
require 'config/config.php';
require 'config/bootstrap.php';
$db = new Database(require 'config/config.php');
$postRepo = new PostRepository($db);

require 'app/models/UserRepository.php';
$userRepo = new UserRepository($db);


$router = require __DIR__ . '/config/routes.php';

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$router->route($uri);
