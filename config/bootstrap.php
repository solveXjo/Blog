<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

define('APP_ROOT', dirname(__DIR__));
define('VIEW_PATH', APP_ROOT . 'resources/views');
define('STORAGE_PATH', APP_ROOT . '/storage');

date_default_timezone_set('UTC');

$config = require 'config/config.php';


require_once  'app/core/Database.php';
require_once  'app/core/Session.php';
require_once  'app/core/Cache.php';


$db = new Database($config);

$session = new Session();

$cache = new Cache(['path' => STORAGE_PATH . '/cache', 'ttl' => 3600]);





function session()
{
    global $session;
    return $session;
}

function cache()
{
    global $cache;
    return $cache;
}

function db()
{
    global $db;
    return $db;
}
function users()
{
    global $userRepo;
    return $userRepo;
}
