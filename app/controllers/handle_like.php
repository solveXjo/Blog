<?php
session_start();
require_once '../../app/core/Database.php';
require_once '../../app/models/PostRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("/home");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $db = new Database(require '../../config/config.php');
    $postRepo = new PostRepository($db);

    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];
    $action = $_POST['action'] ?? 'like';

    if ($action === 'like') {
        $postRepo->addLike($userId, $postId);
    } else {
        $postRepo->removeLike($userId, $postId);
    }

    $likes = $postRepo->getLikeCount($postId);
    echo $likes;
} else {
    echo 0;
}
