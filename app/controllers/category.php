<?php
session_start();
require_once 'app/controllers/Posts.php';



$currentCategory = $_GET['category'] ?? "";
$image = $_GET['image_path'] ?? "";



$categories = $postRepo->getAllCategories();

if ($currentCategory) {
    $posts = $postRepo->getPostsByCategory($currentCategory);
    $pageTitle = ucfirst($currentCategory) . " Posts";
} else {
    $posts = $postRepo->getAllPosts();
}

function merge_query(array $newParams = [])
{
    $currentParams = $_GET;
    $mergedParams = array_merge($currentParams, $newParams);

    $baseUri = "/category";
    if (!empty($currentParams['category'])) {
        $baseUri .= "/" . $currentParams['category'];
    }

    $queryString = http_build_query(array_filter($mergedParams, function ($key) {
        return $key !== 'category';
    }, ARRAY_FILTER_USE_KEY));

    return $queryString ? $baseUri . "?" . $queryString : $baseUri;
}


$perpage = 4;
$query = "SELECT COUNT(*) as total FROM posts";
if (!empty($currentCategory)) {
    $query .= " WHERE category = :category order by id desc";
}
$stmt = $db->connection->prepare($query);
if (!empty($currentCategory)) {
    $stmt->bindParam(':category', $currentCategory, PDO::PARAM_STR);
}
$stmt->execute();
$totalPosts = $stmt->fetchColumn();
$totalPages = ceil($totalPosts / $perpage);



$pageNow = $_GET["page"] ?? 1;

$offset = ($pageNow - 1) * $perpage;

if (!empty($currentCategory)) {
    $sql = "SELECT * FROM posts WHERE category = :category ORDER BY id desc LIMIT $offset, $perpage";
    $stmt = $db->connection->prepare($sql);
    $stmt->bindParam(':category', $currentCategory, PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM posts LIMIT $offset, $perpage";
    $stmt = $db->connection->prepare($sql);
}
$stmt->execute();


$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($posts);


// echo "<pre>category: ";
// print_r($currentCategory);
// echo "</pre>";
// echo "<pre>count: ";
// print_r(count($posts));
// echo "</pre>";
