<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

define('APP_ROOT', dirname(__DIR__));
define('VIEW_PATH', APP_ROOT . '/resources/views'); // Fixed missing '/'.
define('STORAGE_PATH', APP_ROOT . '/storage');

date_default_timezone_set('UTC');

// Load the configuration file
$config = require APP_ROOT . '/config/config.php';

// Include Composer's autoloader
require APP_ROOT . '/vendor/autoload.php';
require_once 'app/core/Database.php';

require 'app/core/Session.php';
require 'app/core/Cache.php';

require 'app/models/UserRepository.php';
require_once 'app/models/PostRepository.php';



// Use namespaces for core classes
use App\Core\Database;
use App\Core\Session;
use App\Core\Cache;
use App\Models\PostRepository;
use App\Models\UserRepository;
// Initialize the components
$db = new Database($config);
$session = new Session();
$cache = new Cache(['path' => STORAGE_PATH . '/cache', 'ttl' => 3600]);

$postRepo = new PostRepository($db);
$userRepo = new UserRepository($db);


// Helper functions for global access
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
