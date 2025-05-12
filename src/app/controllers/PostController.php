<?php


namespace App\Controllers;

use App\Core\BaseController;

use App\Core\Database;

use app\Models\PostRepository;



class PostController extends BaseController
{

    public function show()
    {
        $config = require 'src/config/config.php';
        $db = new Database($config);


        $postRepo = new PostRepository($db);

        $PostController = $this->handleRequest();
        $posts = $postRepo->getAllPosts();
        $getMostRecentPosts = $postRepo->getRecentPosts(5);


        echo $this->view->renderWithLayout('Posts.view.php', 'layouts/main.php', [
            'title' => 'Posts - Altibbi',

            'postData' => $this->postData,
            'PostController' => $PostController,
            'posts' => $posts,
            'getMostRecentPosts' => $getMostRecentPosts
        ]);
    }

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
        // Delete likes associated with the post
        $stmt = $this->db->connection->prepare("DELETE FROM post_likes WHERE post_id = ?");
        $stmt->execute([$postId]);

        // Delete comments associated with the post
        $stmt = $this->db->connection->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->execute([$postId]);

        // Delete the post itself
        $stmt = $this->db->connection->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$postId]);

        return $stmt->rowCount() > 0;
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
