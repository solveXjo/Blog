<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Router;
use App\Models\UserRepository;
use App\Models\PostRepository;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

class App
{
    protected $container = [];

    public function __construct(array $config)
    {
        $this->container['config'] = $config;
        $this->container['db'] = new Database($config);
        $this->container['router'] = new Router();
        $this->container['userRepo'] = new UserRepository($this->container['db']);
        $this->container['postRepo'] = new PostRepository($this->container['db']);

        $cacheConfig = new ConfigurationOption([
            'path' => $config['path'] ?? sys_get_temp_dir()
        ]);
        $this->container['cache'] = CacheManager::getInstance('files', $cacheConfig);

        $routes = require 'src/config/routes.php';
        $routes($this);
    }

    public function __get($name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }

        throw new \RuntimeException("Service {$name} not found in application container");
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->router->route($uri, $_SERVER['REQUEST_METHOD']);
    }
}