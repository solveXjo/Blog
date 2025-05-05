<?php


$db = new Database(require 'config/config.php');
$postRepo = new PostRepository($db);




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $postId = $_POST['post_id'];
    if ($postId !== false) {
        $postRepo->incrementLikes($postId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $postId = $_POST['post_id'];
    $parentId = $_POST['parent_comment_id'];
    $comment = $_POST['comment'];

    if ($postId !== false && !empty($comment)) {
        $postRepo->addComment($postId, $_SESSION['user_id'], $comment, $parentId);
        header("Location: /posts");
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $postId = $_POST['post_id'];

    if ($postId !== false) {
        $postRepo->removePost($postId);
        header("Location: /posts");
        exit();
    }
}

function createSlug($title)
{
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}




$posts = $postRepo->getAllPosts();
$getMostRecentPosts = $postRepo->getRecentPosts(5);
