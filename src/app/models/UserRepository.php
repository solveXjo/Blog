<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getUserById($userId)
    {
        $query = "SELECT 
                users.id, 
                users.name, 
                users.age, 
                users.email, 
                users.bio, 
                users.location,
                users.title,
                media.image_path 
            FROM users 
            LEFT JOIN media ON users.id = media.user_id 
            where users.id = :id";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users where email = :email";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($userId, $name, $title, $age, $email, $bio = null, $location = null)
    {
        $query = "UPDATE users SET 
            name = :name,
            title = :title, 
            age = :age, 
            email = :email,
            bio = :bio,
            location = :location";

        $params = [
            ':name' => $name,
            ':title' => $title,
            ':age' => $age,
            ':email' => $email,
            ':bio' => $bio,
            ':location' => $location,
            ':id' => $userId
        ];


        $query .= " where id = :id";

        $stmt = $this->db->connection->prepare($query);
        return $stmt->execute($params);
    }

    public function changePassword($userId, $newPassword)
    {

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password where id = :id";
        $stmt = $this->db->connection->prepare($query);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

    public function getUser()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /Login");
            exit();
        }
        return $this->getUserById($_SESSION['user_id']);
    }

    public function getProfile()
    {
        $user = $this->getUser();
        if (!$user) {
            header("Location: /Login");
            exit();
        }
        return $user;
    }


    public function updateImage($userId, $imagePath)
    {
        $checkQuery = "SELECT COUNT(*) FROM media where user_id = :user_id";
        $checkStmt = $this->db->connection->prepare($checkQuery);
        $checkStmt->execute(['user_id' => $userId]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            $query = "UPDATE media SET image_path = :image_path where user_id = :user_id";
        } else {
            $query = "INSERT INTO media (user_id, image_path) VALUES (:user_id, :image_path)";
        }

        $stmt = $this->db->connection->prepare($query);
        return $stmt->execute([
            'user_id' => $userId,
            'image_path' => $imagePath
        ]);
    }

    public function getUserImage($userId)
    {
        $query = "SELECT image_path FROM media where user_id = :user_id";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['image_path'] ?? 'default.png';
    }

    public function deleteUser($userId)
    {
        // First delete the media record if it exists
        $image = $this->getUserImage($userId);
        if ($image && $image !== 'default.png') {
            $uploadDir = 'uploads/';
            if (file_exists($uploadDir . $image)) {
                unlink($uploadDir . $image);
            }
        }

        $query = "DELETE from media WHERE user_id = :user_id";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        $query = "DELETE FROM users where id = :id";
        $stmt = $this->db->connection->prepare($query);
        return $stmt->execute(['id' => $userId]);
    }
}
