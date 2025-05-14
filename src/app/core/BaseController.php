<?php

namespace App\Core;

use App\Core\Database;
use App\Core\View;

use App\Models\PostRepository;
use App\Models\UserRepository;

class BaseController
{
    protected $db;
    protected $view;

    protected $postRepo;

    protected $userRepo;
    protected $postData = [];

    public function __construct()
    {
        $config = require 'src/config/config.php';
        $this->db = new Database($config);
        $this->view = new View('src/app/views');

        $this->postData = $_POST ?? [];
    }

    public function getCurrentCategory(): string
    {
        return $_GET['category'] ?? '';
    }
}
