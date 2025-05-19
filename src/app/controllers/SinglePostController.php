<?php

namespace App\Controllers;

use App\Core\BaseController;

class SinglePostController extends BaseController
{
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
        $postId = $this->getPostId();
        $singlepost = $this->app->postRepo->getPostById($postId);

        if (!$singlepost) {
            die("Post not found.");
        }

        $userId = $this->getUserId($singlepost);
        $userInfo = $this->app->userRepo->getUserById($userId);
        $this->view->title = $singlepost['caption'];

        echo $this->view->renderWithLayout('single-post.view.php', 'layouts/main.php', [
            'title' => 'Post - ' . $singlepost['caption'],
            'postId' => $postId,
            'singlepost' => $singlepost,
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
}