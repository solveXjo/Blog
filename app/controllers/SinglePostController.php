<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\PostRepository;
use App\Models\UserRepository;

require_once 'app/controllers/Posts.php';


class SinglePostController
{
    private $postRepo;
    private $userRepo;
    private $db;

    public function __construct(Database $db, PostRepository $postRepo, UserRepository $userRepo)
    {
        $this->db = $db;
        $this->postRepo = new PostRepository($db);
        $this->userRepo = new UserRepository($db);
    }


    public function getPostId()
    {
        $postId = $_GET['id'] ?? null;

        if (!is_numeric($postId)) {
            $postId = substr($_SERVER['REQUEST_URI'], strlen('/post/'));
        }

        if (empty($postId) || !is_numeric(value: $postId)) {
            die("Invalid post ID.");
        }

        return $postId;
    }
    public function getSinglePost($postId)
    {
        $singlepost = $this->postRepo->getPostById($postId);

        if (!$singlepost) {
            die("Post not found.");
        }

        return $singlepost;
    }

    public function getUserId($singlepost)
    {
        $userId = $singlepost['user_id'];

        if (empty($userId) || !is_numeric(value: $userId)) {
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
