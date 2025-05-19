<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PostRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }


    public function getPostCount()
    {
        $stmt = $this->db->connection->prepare("SELECT COUNT(*) as count FROM posts");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
    public function getAllPosts()
    {
        $query = "SELECT posts.*, users.name FROM posts INNER JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
        $posts = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as &$post) {
            if (strlen($post['caption']) > 300) {
                $post['caption'] = substr($post['caption'], 0, 300) . '...';
            }
        }

        return $posts;
    }


    public function getPostById($postId)
    {
        $query = "SELECT posts.*, users.name FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.id = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getPostByCaption($caption)
    {
        $query = "SELECT posts.*, users.name FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.caption = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$caption]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getUserById($userId)
    {
        $query = "SELECT name FROM users WHERE id = :user_id LIMIT 1";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
    public function createPost($userId, $caption, $description, $quote, $category, $imagePath = null)
    {
        // Get user name
        $user = $this->getUserById($userId);
        $name = $user['name'];

        $query = "INSERT INTO posts 
                 (user_id, name, caption, description, quote, category, image_path, likes, created_at) 
                 VALUES 
                 (:user_id, :name, :caption, :description, :quote, :category, :image_path, 0, NOW())";

        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([
            'user_id' => $userId,
            'name' => $name,
            'caption' => $caption,
            'description' => $description,
            'quote' => $quote,
            'category' => $category,
            'image_path' => $imagePath
        ]);

        return $this->db->connection->lastInsertId();
    }


    public function getPostBySlug($slug)
    {
        $statement = $this->db->connection->prepare("
            SELECT *
            FROM posts
            WHERE slug = :slug
        ");

        $statement->execute(['slug' => $slug]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function createSlug($title, $id = null)
    {
        // Convert title to slug
        $slug = $this->convertTitleToSlug($title);

        // Check if slug already exists
        $statement = $this->db->connection->prepare("
            SELECT COUNT(*) as count
            FROM posts
            WHERE slug = :slug
            " . ($id ? "AND id != :id" : "") . "
        ");

        $params = ['slug' => $slug];
        if ($id) {
            $params['id'] = $id;
        }

        $statement->execute($params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // If slug exists, append a number
        if ($result['count'] > 0) {
            $i = 1;
            $originalSlug = $slug;

            do {
                $slug = $originalSlug . '-' . $i++;

                $statement = $this->db->connection->prepare("
                    SELECT COUNT(*) as count
                    FROM posts
                    WHERE slug = :slug
                    " . ($id ? "AND id != :id" : "") . "
                ");

                $params = ['slug' => $slug];
                if ($id) {
                    $params['id'] = $id;
                }

                $statement->execute($params);
                $result = $statement->fetch(PDO::FETCH_ASSOC);
            } while ($result['count'] > 0);
        }

        return $slug;
    }

    public function convertTitleToSlug($str)
    {
        // Convert string to lowercase
        $str = strtolower($str);

        // Replace the spaces with hyphens
        $str = str_replace(' ', '-', $str);

        // Remove the special characters
        $str = preg_replace('/[^a-z0-9\-]/', '', $str);

        // Remove the consecutive hyphens
        $str = preg_replace('/-+/', '-', $str);

        // Trim hyphens from the beginning
        // and ending of String
        $str = trim($str, '-');

        return $str;
    }

    public function updatePostSlug($id, $title)
    {
        // Generate slug from title
        $slug = $this->createSlug($title, $id);

        // Update post with new slug
        $statement = $this->db->connection->prepare("
            UPDATE posts
            SET slug = :slug
            WHERE id = :id
        ");

        $statement->execute([
            'id' => $id,
            'slug' => $slug
        ]);

        return $slug;
    }

    public function generateSlugsForAllPosts()
    {
        $statement = $this->db->connection->prepare("
            SELECT id, caption
            FROM posts
            WHERE slug IS NULL OR slug = ''
        ");

        $statement->execute();
        $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $post) {
            $this->updatePostSlug($post['id'], $post['caption']);
        }

        return count($posts);
    }
    public function incrementLikes($postId)
    {
        $query = "SELECT liked FROM posts WHERE id = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$postId]);
        $currentStatus = $stmt->fetchColumn();

        $newStatus = $currentStatus ? 0 : 1;

        $query = "UPDATE posts SET likes = likes + ?, liked = ? WHERE id = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([
            $newStatus ? 1 : -1,
            $newStatus,
            $postId
        ]);
    }

    public function removePost($postId)
    {
        $stmt = $this->db->connection->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->execute([$postId]);

        $stmt = $this->db->connection->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$postId]);

        return $stmt->rowCount() > 0;
    }

    public function addComment($postId, $userId, $comment, $parentId = null)
    {
        $query = "INSERT INTO comments (post_id, user_id, comment, parent_comment_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$postId, $userId, $comment, $parentId]);
        return $stmt->rowCount() > 0;
    }

    public function getAllComments($postId)
    {
        // Get all comments with user info and profile image
        $query = "SELECT c.*, u.name, m.image_path 
                  FROM comments c
                  INNER JOIN users u ON c.user_id = u.id
                  INNER JOIN media m ON c.user_id = m.user_id
                  WHERE c.post_id = ? 
                  ORDER BY COALESCE(c.parent_comment_id, c.id), c.created_at";

        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$postId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Organize comments hierarchically
        $organized = [];
        foreach ($comments as $comment) {
            if ($comment['parent_comment_id'] === null) {
                // Parent comment
                $organized[$comment['id']] = $comment;
                $organized[$comment['id']]['replies'] = [];
            }
        }

        // Add replies to parents
        foreach ($comments as $comment) {
            if ($comment['parent_comment_id'] !== null && isset($organized[$comment['parent_comment_id']])) {
                $organized[$comment['parent_comment_id']]['replies'][] = $comment;
            }
        }

        return array_values($organized);
    }

    public function updateComment($commentId, $newText)
    {
        $query = "UPDATE comments SET comment = ? WHERE id = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$newText, $commentId]);
        return $stmt->rowCount() > 0;
    }

    public function deleteComment($commentId)
    {
        $query = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$commentId]);
        return $stmt->rowCount() > 0;
    }
    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getMostLikedPosts($limit = 3)
    {
        $query = "SELECT * from posts ORDER BY likes DESC LIMIT ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentPosts($limit = 5)
    {
        $query = "SELECT * from posts order by created_at desc limit ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addLike($userId, $postId)
    {
        if (!$this->hasUserLikedPost($userId, $postId)) {
            $stmt = $this->db->connection->prepare(
                "INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)"
            );
            $stmt->execute([$userId, $postId]);

            $stmt = $this->db->connection->prepare(
                "UPDATE posts SET likes = likes + 1 WHERE id = ?"
            );
            $stmt->execute([$postId]);
        }
    }

    public function removeLike($userId, $postId)
    {
        $stmt = $this->db->connection->prepare(
            "DELETE FROM post_likes WHERE user_id = ? AND post_id = ?"
        );
        $stmt->execute([$userId, $postId]);

        $stmt = $this->db->connection->prepare(
            "UPDATE posts SET likes = GREATEST(0, likes - 1) WHERE id = ?"
        );
        $stmt->execute([$postId]);
    }

    public function getLikeCount($postId)
    {
        $stmt = $this->db->connection->prepare(
            "SELECT likes FROM posts WHERE id = ?"
        );
        $stmt->execute([$postId]);
        return $stmt->fetchColumn();
    }
    public function hasUserLikedPost($userId, $postId)
    {
        $stmt = $this->db->connection->prepare(
            "SELECT COUNT(*) FROM post_likes WHERE user_id = ? AND post_id = ?"
        );
        $stmt->execute([$userId, $postId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getPostsByCategory($category)
    {
        $query = "SELECT * FROM posts 
                  WHERE category = ?
                  ORDER BY created_at DESC";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCategories()
    {
        $query = "SELECT category, COUNT(*) as count FROM posts GROUP BY category";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function postExists($postId)
    {
        $query = "SELECT COUNT(*) FROM posts WHERE id = :id";
        $stmt = $this->db->connection->prepare($query);
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
