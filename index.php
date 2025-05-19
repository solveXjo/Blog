<?php

require 'vendor/autoload.php';
require 'src/config/bootstrap.php';

$config = require "src/config/config.php";
$app = new \App\Core\App($config);
$app->run();


