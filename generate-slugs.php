<?php
// generate-slugs.php: Run this script once to generate slugs for all existing posts

require 'vendor/autoload.php';
$config = require "src/config/config.php";

$db = new App\Core\Database($config);
$postRepo = new App\Models\PostRepository($db);

$updatedCount = $postRepo->generateSlugsForAllPosts();

echo "Generated slugs for {$updatedCount} posts.\n";
