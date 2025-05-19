<?php

namespace App\Core;

use App\Core\Database;
use App\Core\View;

use App\Models\PostRepository;
use App\Models\UserRepository;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

class BaseController
{
    protected $app;
    protected $view;


    public function __construct()
    {
        $this->app = $GLOBALS['app'];
        $this->view = new View();

    }


    public function getCurrentCategory(): string
    {
        return $_GET['category'] ?? '';
    }
}
