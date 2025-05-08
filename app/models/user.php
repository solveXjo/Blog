<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getUserById($userId)
    {
        $query = "
            SELECT users.id, users.name, users.age, users.email, media.image_path 
            FROM users 
            LEFT JOIN media ON users.id = media.user_id 
            WHERE users.id = :id
        ";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($userId, $name, $age, $email, $password = null)
    {
        $query = "UPDATE users SET name = :name, age = :age, email = :email";
        $params = [':name' => $name, ':age' => $age, ':email' => $email];

        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password = :password";
            $params[':password'] = $hashedPassword;
        }

        $query .= " WHERE id = :id";
        $params[':id'] = $userId;

        $this->db->query($query, $params);
    }

    public function updateImage($userId, $imagePath)
    {
        $query2 = "SELECT COUNT(*) FROM media WHERE user_id = :user_id";
        $stmt = $this->db->connection->prepare($query2);
        $stmt->execute(['user_id' => $userId]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            $query = "UPDATE media SET image_path = :image_path WHERE user_id = :user_id";
        } else {
            $query = "INSERT INTO media (user_id, image_path) VALUES (:user_id, :image_path)";
        }

        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['user_id' => $userId, 'image_path' => $imagePath]);
    }

    public function getAllPosts()
    {
        $query = "SELECT posts.*, users.name FROM posts INNER JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
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

    public function findUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
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
        $query = "SELECT comments.*, users.name from comments INNER JOIN users on comments.user_id = users.id where post_id = ? ORDER BY created_at desc";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $query = "DELETE FROM comments WHERE id = ? OR parent_comment_id = ?";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([$commentId, $commentId]);
        return $stmt->rowCount() > 0;
    }
    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($name, $age, $email, $password, $image)
    {
        $query = "INSERT INTO users (name, age, email, password, image) VALUES (:name, :age, :email, :password, :image)";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute([
            'name' => $name,
            'age' => $age,
            'email' => $email,
            'password' => $password,
            'image' => $image
        ]);
    }

    public function uploadImage($file)
    {
        $filename = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileExt = explode('.', $filename);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileActualExt, $allowed)) {
            return false;
        }

        if ($fileError !== 0) {
            return false;
        }

        $fileNameNew = uniqid('', true) . '.' . $fileActualExt;
        $fileDestination = '../../uploads/' . $fileNameNew;
        move_uploaded_file($fileTmpName, $fileDestination);

        return $fileNameNew;
    }
}
