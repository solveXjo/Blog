<?php
require 'vendor/autoload.php';




$config = require 'src/config/config.php';
$db = new App\Core\Database($config);
$postRepo = new App\Models\PostRepository($db);
$catController = new App\Controllers\CategoryController($db, $postRepo);



$postController = new  App\Controllers\PostController();

$currentCategory = $catController->getCurrentCategory();
$categories = $postRepo->getAllCategories();
$pageTitle = $catController->getPageTitle($currentCategory);


$paginationData = $catController->getPaginatedPosts($currentCategory);
$posts = $paginationData['posts'];
$totalPages = $paginationData['totalPages'];
$currentPage = $paginationData['currentPage'];

$merge_query = [$catController, 'mergeQuery'];
?>

<head>
    <?php include 'src/Partials/head.php' ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body class="category-page">
    <?php include 'src/Partials/nav.php' ?>

    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mt-3">
                        <?= htmlspecialchars($pageTitle) ?>
                    </h2>

                    <!-- Blog Posts Section -->
                    <section id="blog-posts" class="blog-posts section">
                        <div class="container">
                            <?php if (empty($posts)): ?>
                                <div class="alert alert-info">
                                    No posts found <?= $currentCategory ? 'in the "' . htmlspecialchars($currentCategory) . '" category' : '' ?>.
                                </div>
                            <?php endif; ?>

                            <div class="row gy-4">
                                <?php foreach ($posts as $post): ?>
                                    <?php if (!empty($post)): ?>
                                        <div class="col-lg-6">
                                            <article class="position-relative h-100">
                                                <div class="post-img position-relative overflow-hidden">
                                                    <img src="src/<?= !empty($post['image_path']) ? htmlspecialchars($post['image_path']) : 'public/images/download.png' ?>"
                                                        class="img-fluid" alt="" style="max-width: 460px;">
                                                    <?php if (!empty($post['created_at'])): ?>
                                                        <span class="post-date"><?= date("F j", strtotime($post['created_at'])) ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="post-content d-flex flex-column">
                                                    <?php if (!empty($post['id']) && !empty($post['caption'])): ?>
                                                        <a href="/post/<?= $post['id'] ?>/<?= $postController->createSlug($post['caption']) ?>">
                                                            <h3 class="post-title"><?= htmlspecialchars($post['caption']) ?></h3>
                                                        </a>
                                                    <?php endif; ?>
                                                    <div class="meta d-flex align-items-center">
                                                        <?php if (!empty($post['name'])): ?>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa fa-user"></i>
                                                                <span class="ps-2"><?= htmlspecialchars($post['name']) ?></span>
                                                            </div>
                                                            <span class="px-3 text-black-50">/</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($post['category'])): ?>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa fa-folder"></i>
                                                                <span class="ps-2"><?= htmlspecialchars($post['category']) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Blog Pagination Section -->
                    <?php if ($totalPages > 1): ?>
                        <section id="blog-pagination" class="blog-pagination section">
                            <div class="container">
                                <div class="d-flex justify-content-center">
                                    <ul>
                                        <?php if ($currentPage > 1): ?>
                                            <li>
                                                <a href="<?= call_user_func($merge_query, ['page' => $currentPage - 1]) ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li>
                                                <a href="<?= call_user_func($merge_query, ['page' => $i]) ?>"
                                                    class="<?= $i == $currentPage ? 'active' : '' ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($currentPage < $totalPages): ?>
                                            <li>
                                                <a href="<?= call_user_func($merge_query, ['page' => $currentPage + 1]) ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4 sidebar">
                    <div class="widgets-container">
                        <div class="categories-widget widget-item">
                            <h3 class="widget-title">Categories</h3>
                            <ul class="mt-3">
                                <li>
                                    <a href="/category" class="<?= empty($currentCategory) ? 'active' : '' ?>">
                                        All Categories <span>(<?= array_sum(array_column($categories, 'count')) ?>)</span>
                                    </a>
                                </li>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if (!empty($cat['category'])): ?>
                                        <li>
                                            <a href="/category/<?= urlencode($cat['category']) ?>"
                                                class="<?= $currentCategory === $cat['category'] ? 'active' : '' ?>">
                                                <?= htmlspecialchars($cat['category']) ?>
                                                <span>(<?= $cat['count'] ?? 0 ?>)</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'src/Partials/footer.php' ?>
</body>

</html>