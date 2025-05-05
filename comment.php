<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /index");
    exit();
}

require_once 'app/core/Database.php';
require_once 'app/models/PostRepository.php';
require_once 'app/models/UserRepository.php';

$config = require 'config/config.php';

$db = new Database($config);
$postRepo = new PostRepository($db);
$userRepo = new UserRepository($db);


$userId = $_SESSION['user_id'];
$postId = $_GET['post_id'] ?? null;
$postDetails = $postRepo->getPostById($postId);
$caption = $postDetails['caption'] ?? '';

if (strlen($caption) > 50) {
    $caption = substr($caption, 0, 50) . '...';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['comment'])) {
    $postId = $_POST['post_id'];
    $commentText = $_POST['comment'];
    $parentId = $_POST['parent_id'] ?? null;

    if ($postId && !empty($commentText)) {
        $postRepo->addComment($postId, $userId, $commentText, $parentId);
        header("Location: /comment/" . $postId);
        exit();
    }
}

//reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $postId = $_GET['post_id'];
    $commentText = $_POST['reply'];
    $parentId = $_POST['parent_id'];

    if ($postId && $parentId && !empty($commentText)) {
        $postRepo->addComment($postId, $userId, $commentText, $parentId);
        header("Location: /comment/" . $postId);
        exit();
    }
}

// comment edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
    $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    $newText = trim($_POST['new_comment']);

    if ($commentId && !empty($newText)) {
        $postRepo->updateComment($commentId, $newText);
        header("Location: /comment/" . $postId);
        exit();
    }
}

// comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $postId = $_GET['post_id'];
    $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    if ($commentId !== null) {
        $postRepo->deleteComment($commentId);
        header("Location: /comment/" . $postId);
        exit();
    }
}

$comments = $postId ? $postRepo->getAllComments($postId) : [];
$commentCount = $comments ? count($comments) : 0;

$userInfo = $userRepo->getUserById($userId);
