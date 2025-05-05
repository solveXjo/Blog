<?php require 'app/controllers/category.php'; ?>

<?php
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;


?>

<head>
    <?php include 'Partials/head.php' ?>
    <title>Category</title>
</head>

<body class="category-page">


    <?php include 'Partials/nav.php' ?>




    <main class="main">
        <div class="container">

            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mt-3">
                        <?= htmlspecialchars($currentCategory ?? 'All Categories') ?>
                    </h2>

                    <!-- Blog Posts Section -->
                    <section id="blog-posts" class="blog-posts section">
                        <div class="container">
                            <?php if ($currentCategory && empty($posts)): ?>
                                <div class="alert alert-info">
                                    No posts found in the "<?= $currentCategory ?>" category in page <?= $currentPage ?>.
                                </div>
                            <?php endif; ?>

                            <div class="row gy-4">
                                <?php foreach ($posts as $post): ?>
                                    <div class="col-lg-6">
                                        <article class="position-relative h-100">
                                            <div class="post-img position-relative overflow-hidden">
                                                <img src="<?= !empty($post['image_path']) ? $post['image_path'] : 'public/images/download.png' ?>"
                                                    class="img-fluid" alt="" style="max-width: 460px;">
                                                <span class="post-date"><?= date("F j", strtotime($post["created_at"])) ?></span>
                                            </div>

                                            <div class="post-content d-flex flex-column">
                                                <a href="/post/<?= $post['id'] ?>/<?= createSlug($post['caption']) ?>">
                                                    <h3 class="post-title"><?= htmlspecialchars($post["caption"]) ?></h3>
                                                </a>
                                                <div class="meta d-flex align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-user"></i>
                                                        <span class="ps-2"><?= htmlspecialchars($post["name"]) ?></span>
                                                    </div>
                                                    <span class="px-3 text-black-50">/</span>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-folder"></i>
                                                        <span class="ps-2"><?= htmlspecialchars($post["category"]) ?></span>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                        </article>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                </div>

                <div class="col-lg-4 sidebar">

                    <div class="widgets-container">

                        <!-- Blog Author Widget 2 -->
                        <!-- <div class="blog-author-widget-2 widget-item">
                            <div class="d-flex flex-column align-items-center">
                                <img src="<?= $post["image_path"] ?? "images/download.png" ?> " class="rounded-circle flex-shrink-0" alt="">
                                <h4><?= $post['name'] ?></h4>
                                <div class="social-links">
                                    <a href="https://x.com/#" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
                                    <a href="https://facebook.com/#" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://instagram.com/#" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                                    <a href="https://linkedin.com/#" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                                <p>
                                    <?= htmlspecialchars($post["caption"]) ?>
                                </p>
                            </div>
                        </div>/Blog Author Widget 2 -->

                        <div class="categories-widget widget-item">
                            <h3 class="widget-title">Categories</h3>
                            <ul class="mt-3">
                                <li>
                                    <a href="/category" class="<?= empty($currentCategory) ? 'active' : '' ?>">
                                        All Categories <span>(<?= array_sum(array_column($categories, 'count')) ?>)</span>
                                    </a>
                                </li>
                                <?php foreach ($categories as $cat): ?>
                                    <li>
                                        <a href="/category/<?= urlencode($cat['category']) ?>"
                                            class="<?= isset($currentCategory) && $currentCategory === $cat['category'] ? 'active' : '' ?>">
                                            <?= htmlspecialchars($cat['category']) ?>
                                            <span>(<?= $cat['count'] ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>


                    </div>

                </div>
                <!-- Blog Pagination Section -->
                <section id="blog-pagination" class="blog-pagination section">
                    <div class="container">
                        <div class="d-flex justify-content-center">
                            <ul>
                                <?php


                                if ($currentPage > 1): ?>
                                    <li>
                                        <a href="<?= merge_query(['page' => $currentPage - 1]) ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif;

                                for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li>
                                        <a href="<?= merge_query(['page' => $i]) ?>"
                                            class="<?= $i == $currentPage ? 'active' : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor;

                                if ($currentPage < $totalPages): ?>
                                    <li>
                                        <a href="<?= merge_query(['page' => $currentPage + 1]) ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>

        </div>

    </main>

    <?= include 'Partials/footer.php' ?>


</body>

</html>