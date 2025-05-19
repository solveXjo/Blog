<?php

date_default_timezone_set('UTC');

$config = require __DIR__ . '/config.php';

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Drivers\Files\Config as FilesConfig;
use App\Core\Database;
use App\Core\Session;
use App\Core\Cache;
use App\Models\PostRepository;
use App\Models\UserRepository;

$db = new Database($config);
$session = new Session();

$cacheDir = __DIR__ . '/../../cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Set up default configuration
$defaultConfig = new ConfigurationOption([
    'path' => $cacheDir,
]);

$filesConfig = new FilesConfig([
    'path'              => $cacheDir,
    'preventCacheSlams' => true,
    'cacheSlamsTimeout' => 20,
]);

CacheManager::setDefaultConfig($defaultConfig);
$cacheInstance = CacheManager::getInstance('Files', $filesConfig);

$cache = new Cache($cacheInstance);

$postRepo = new PostRepository($db);
$userRepo = new UserRepository($db);

class AppContainer {
    public $db;
    public $session;
    public $cache;
    public $postRepo;
    public $userRepo;

    public function __construct($db, $session, $cache, $postRepo, $userRepo) {
        $this->db = $db;
        $this->session = $session;
        $this->cache = $cache;
        $this->postRepo = $postRepo;
        $this->userRepo = $userRepo;
    }
}

$app = new AppContainer($db, $session, $cache, $postRepo, $userRepo);
