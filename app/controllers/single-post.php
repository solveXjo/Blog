<?php
require_once 'app/controllers/Posts.php';
$userRepo = new UserRepository($db);

$postId = $_GET['id'] ?? null;



if (!is_numeric($postId)) {
    $postId = substr($_SERVER['REQUEST_URI'], strlen('/post/'));
}

if (empty($postId) || !is_numeric(value: $postId)) {
    die("Invalid post ID.");
}

$singlepost = $postRepo->getPostById($postId);

if (!$singlepost) {
    die("Post not found.");
}

$userId = $singlepost['user_id'];
$userInfo = $userRepo->getUserById($userId);




if (!$singlepost) {
    die("Post not found.");
}

$userId = $singlepost['user_id'];
$userInfo = $userRepo->getUserById($userId);
