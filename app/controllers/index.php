<?php
session_start();


$db = new Database(require 'config/config.php');
$postRepo = new PostRepository($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /Login");
        exit();
    }


    $caption = trim($_POST['caption']);
    $description = trim($_POST['description']);
    $quote = trim($_POST['quote']);
    $category = $_POST["category"] ?? 'others';
    $user_id = $_SESSION['user_id'];

    if (empty($caption)) {
        header("Location: /home");
        exit();
    }
    if (strlen($caption) > 50) {
        $caption = substr($caption, 0, 50) . '...';
    }

    // Handle image upload if present
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = $destination;
        }
    }

    $query = "SELECT name FROM users WHERE id = :user_id";
    $stmt = $db->connection->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();
    $name = $user['name'];

    $query = "INSERT INTO posts (user_id, name, caption, likes, created_at, category, image_path, quote, description) 
              VALUES (:user_id, :name, :caption, 0, NOW(), :category, :image_path, :quote, :description)";
    $stmt = $db->connection->prepare($query);

    $stmt->execute([
        'user_id' => $user_id,
        'name' => $name,
        'caption' => $caption,
        'category' => $category,
        'image_path' => $imagePath,
        'quote' => $quote,
        'description' => $description
    ]);

    header("Location: /posts");
    exit();
}
$allPosts = $postRepo->getAllPosts();
$mostLikedPosts = $postRepo->getMostLikedPosts(5);
