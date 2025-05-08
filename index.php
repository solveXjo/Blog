<?php

require 'src/config/bootstrap.php';
require 'vendor/autoload.php';

$router = require 'src/config/routes.php';

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$router->route($uri);
