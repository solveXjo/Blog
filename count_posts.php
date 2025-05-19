<?php


use App\Core\App;
use App\Models\PostRepository;
use App\Core\Database;

require 'vendor/autoload.php';
$db = new Database(require 'src/config/config.php');
$postRepo = new PostRepository($db);

try {
    $postCount = $postRepo->getPostCount();

    $logMessage = date('Y-m-d H:i:s') . " - Total posts: " . $postCount . "\n";
    file_put_contents('/Users/fathi/Desktop/Alttibi_Training/php_project/crud_php/logs/post_count.log', $logMessage, FILE_APPEND);

    echo "Post count logged successfully: " . $postCount . " in time" . date('Y-m-d H:i:s') . "\n";
} catch (Exception $e) {
    echo "Error counting posts: " . $e->getMessage();
}
