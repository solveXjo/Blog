<?php


namespace App\Controllers;




class PostController
{
    private $db;
    private $postRepo;



    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['like'])) {
                $this->addLike($_POST['post_id']);
            } elseif (isset($_POST['add_comment'])) {
                $this->addComment($_POST['post_id'], $_POST['parent_comment_id'] ?? null, $_POST['comment']);
            } elseif (isset($_POST['delete_post'])) {
                $this->deletePost($_POST['post_id']);
            }
        }
    }

    public function addLike($postId)
    {
        if ($postId !== false) {
            $this->postRepo->incrementLikes($postId);
        }
    }

    public function addComment($postId, $parentId, $comment)
    {
        if ($postId !== false && !empty($comment)) {
            $this->postRepo->addComment($postId, $_SESSION['user_id'], $comment, $parentId);
            header("Location: /posts");
            exit();
        }
    }

    public function deletePost($postId)
    {
        if ($postId !== false) {
            $this->postRepo->removePost($postId);
            header("Location: /posts");
            exit();
        }
    }

    public function createSlug($title)
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    public function getAllPosts()
    {
        return $this->postRepo->getAllPosts();
    }

    public function getMostRecentPosts($limit)
    {
        return $this->postRepo->getRecentPosts($limit);
    }
}
