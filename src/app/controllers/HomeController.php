<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\PostRepository;

class HomeController extends BaseController
{


public function show()
{
$postRepo = new PostRepository($this->app->db);
$allPosts = $postRepo->getAllPosts();
$mostLikedPosts = $postRepo->getMostLikedPosts(5);

echo $this->view->renderWithLayout('index.view.php', 'layouts/main.php', [
    $this->view->title = 'Home - Altibbi',
'allPosts' => $allPosts,
'mostLikedPosts' => $mostLikedPosts
]);
}

public function handlePostRequest()
{
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
header('Location: /home');
exit();
}

$caption = trim($_POST['caption'] ?? '');
$description = trim($_POST['description'] ?? '');
$quote = trim($_POST['quote'] ?? '');
$category = $_POST['category'] ?? 'others';

if (!isset($_SESSION['user_id'])) {
header("Location: /Login");
exit();
}

$user_id = $_SESSION['user_id'];

if (empty($caption)) {
$_SESSION['form_error'] = "Caption is required";
header("Location: /home");
exit();
}

// Handle image upload
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

try {
$postRepo = new PostRepository($this->app->db);
$postRepo->createPost($user_id, $caption, $description,$quote, $category ,$imagePath );

$_SESSION['form_success'] = "Post created successfully!";
header("Location: /posts");
exit();
} catch (\Exception $e) {
$_SESSION['form_error'] = "Error creating post: " . $e->getMessage();
header("Location: /home");
exit();
}
}
}