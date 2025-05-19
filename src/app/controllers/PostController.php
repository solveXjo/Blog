<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Route;

class PostController extends BaseController
{
    public function show()
    {
        $posts = $this->getCachedData('all_posts', 3600, function() {
            return $this->app->postRepo->getAllPosts();
        });

        $recentPosts = $this->getCachedData('recent_posts', 1800, function() {
            return $this->app->postRepo->getRecentPosts(5);
        });

        $PostController = $this->handleRequest();

        $this->view->title = "test title";
        echo $this->view->renderWithLayout('Posts.view.php', 'layouts/main.php', [
            'PostController' => $PostController,
            'posts' => $posts,
            'getMostRecentPosts' => $recentPosts
        ]);
    }


    private function getCachedData(string $key, int $ttl, callable $callback)
    {
        $cacheItem = $this->app->cache->getItem($key);

        if (!$cacheItem->isHit()) {
            $data = $callback();
            $cacheItem->set($data)->expiresAfter($ttl);
            $this->app->cache->save($cacheItem);
            return $data;
        }

        return $cacheItem->get();
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['like'])) {
                $this->addLike($_POST['post_id']);
                $this->invalidateCache();
            } elseif (isset($_POST['add_comment'])) {
                $this->addComment($_POST['post_id'], $_POST['parent_comment_id'] ?? null, $_POST['comment']);
                $this->invalidateCache();
            } elseif (isset($_POST['delete_post'])) {
                $this->deletePost($_POST['post_id']);
                $this->invalidateCache();
            }
        }
    }

    private function invalidateCache()
    {
        $this->app->cache->deleteItem('all_posts');
        $this->app->cache->deleteItem('recent_posts');
    }

    public function addLike($postId)
    {
        if ($postId !== false) {
            $this->app->postRepo->incrementLikes($postId);
        }
    }

    public function addComment($postId, $parentId, $comment)
    {
        if ($postId !== false && !empty($comment)) {
            $this->app->postRepo->addComment($postId, $_SESSION['user_id'], $comment, $parentId);
            header("Location: /posts");
            exit();
        }
    }

    public function deletePost($postId)
    {
        $stmt = $this->app->db->connection->prepare("DELETE FROM post_likes WHERE post_id = ?");
        $stmt->execute([$postId]);

        $stmt = $this->app->db->connection->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->execute([$postId]);

        $stmt = $this->app->db->connection->prepare("DELETE FROM posts WHERE id = ?");
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
        return $this->getCachedData('all_posts', 3600, function() {
            return $this->app->postRepo->getAllPosts();
        });
    }

    public function getMostRecentPosts($limit)
    {
        $cacheKey = "recent_posts_$limit";
        return $this->getCachedData($cacheKey, 1800, function() use ($limit) {
            return $this->app->postRepo->getRecentPosts($limit);
        });
    }
}