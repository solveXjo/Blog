<?php

namespace App\Controllers;

use App\Core\BaseController;


require_once 'src/app/controllers/Posts.php';


class SinglePostController extends BaseController
{




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
    public function show()
    {

        echo $this->view->renderWithLayout('single-post.view.php', 'layouts/main.php', [
            'title' => 'Post - Altibbi',
            'page Title' => "post",

            'postData' => $this->postData
        ]);
    }
    // public function getSinglePost($postId)
    // {
    //     $singlepost = $this->postRepo->getPostById($postId);

    //     if (!$singlepost) {
    //         die("Post not found.");
    //     }

    //     return $singlepost;
    // }

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
