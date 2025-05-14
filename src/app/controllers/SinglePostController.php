<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;
use App\Models\PostRepository;
use App\Models\UserRepository;
use App\Core\Route;

class SinglePostController extends BaseController
{
    protected $postRepo;
    protected $userRepo;

    public function __construct()
    {
        parent::__construct();
        $config = require "src/config/config.php";
        $db = new Database($config);
        $this->postRepo = new PostRepository($db);
        $this->userRepo = new UserRepository($db);
    }

    public function getPostId()
    {
        $postId = $_GET['id'] ?? null;

        if (!is_numeric($postId)) {
            // Extract ID from URL for /post/123-title format
            $path = $_SERVER['REQUEST_URI'];
            if (preg_match('|/post/(\d+)|', $path, $matches)) {
                $postId = $matches[1];
            }
        }

        if (empty($postId) || !is_numeric($postId)) {
            die("Invalid post ID.");
        }

        return $postId;
    }

    public function show()
    {
        $config = require "src/config/config.php";

        $db = new Database($config);
        $postRepo = new PostRepository($db);
        $userRepo = new UserRepository($db);
        $SP = $this;

        $postId = $SP->getPostId();
        $singlepost = $postRepo->getPostById($postId);



        $userId = $SP->getUserId($singlepost);
        $userInfo = $userRepo->getUserById($userId);


        $userInfo = $userRepo->getUserById($userId);
        if (!$singlepost) {
            die("Post not found.");
        }



        echo $this->view->renderWithLayout('single-post.view.php', 'layouts/main.php', [
            'title' => 'post',
            'postData' => $this->postData,
            'postId' => $postId,
            'singlepost' => $singlepost,
            'userId' => $userId,
            'userInfo' => $userInfo

        ]);
    }

    public function getUserId($singlepost)
    {
        $userId = $singlepost['user_id'];

        if (empty($userId) || !is_numeric($userId)) {
            die("Invalid user ID.");
        }

        return $userId;
    }

    public function getUserInfo($userId)
    {
        $userInfo = $this->userRepo->getUserById($userId);

        if (!$userInfo) {
            die("User not found.");
        }

        return $userInfo;
    }
}
