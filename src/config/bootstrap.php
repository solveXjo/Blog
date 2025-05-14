<?php
date_default_timezone_set('UTC');

$config = require 'src/config/config.php';

require_once 'src/app/core/Database.php';
require 'src/app/core/Session.php';
require 'src/app/core/Cache.php';

require 'src/app/models/UserRepository.php';
require_once 'src/app/models/PostRepository.php';

use App\Core\Database;
use App\Core\Session;
use App\Core\Cache;
use App\Models\PostRepository;
use App\Models\UserRepository;

$db = new Database($config);
$session = new Session();
$cache = new Cache(['path' => 'storage/cache', 'ttl' => 3600]);

$postRepo = new PostRepository($db);
$userRepo = new UserRepository($db);
