<?php

require 'config/config.php';
require 'config/bootstrap.php';
require_once 'app/core/Database.php';


$router = require __DIR__ . '/config/routes.php';

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$router->route($uri);
