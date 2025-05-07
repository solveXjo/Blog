<?php
require "app/controllers/ProfileController.php";
require_once "app/core/Database.php";
require_once "app/models/UserRepository.php";
$db = new Database(require 'config/config.php');


$userRepo = new UserRepository($db);

$PC = new ProfileController($db, $userRepo);

$user = $PC->getProfile();


$PC->handleProfileUpdate($user['id']);

?>

<head>
    <?php include 'partials/head.php'; ?>
    <title><?= htmlspecialchars($user['name'] ?? 'User') ?> | Profile</title>

</head>

<body>

    <?php require 'partials/nav.php'; ?>
    <?php include "Partials/pageTitle.php"; ?>




    <main class="main">



        <section id="author-profile" class="author-profile section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="author-profile-1">

                    <div class="row">
                        <!-- Author Info -->
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <div class="author-card" data-aos="fade-up">
                                <div class="author-image">
                                    <form method="post" enctype="multipart/form-data" class="image-upload-form">
                                        <label for="image" class="btn" style="margin-top: 70px;">
                                            <img src="uploads/<?= !empty($user['image_path']) ? htmlspecialchars($user['image_path']) : 'default.png' ?>" />
                                        </label>

                                        <input type="file" id="image" name="image" accept="image/*" style="display: none;"
                                            onchange="this.form.submit()">
                                    </form>


                                </div>





                                <div class="author-info">
                                    <h2><?= htmlspecialchars($user['name'] ?? 'User') ?></h2>

                                    <p class="designation"><?= htmlspecialchars($user['title'] ?? 'User') ?></p>



                                    <div class="author-stats d-flex justify-content-between text-center my-4">
                                        <div class="stat-item">
                                            <h4 data-purecounter-start="0" data-purecounter-end="147" data-purecounter-duration="1" class="purecounter">147</h4>
                                            <p>Articles</p>
                                        </div>
                                        <div class="stat-item">
                                            <h4 data-purecounter-start="0" data-purecounter-end="13" data-purecounter-duration="1" class="purecounter">13</h4>
                                            <p>Awards</p>
                                        </div>
                                        <div class="stat-item">
                                            <h4 data-purecounter-start="0" data-purecounter-end="25" data-purecounter-duration="1" class="purecounter">25K</h4>
                                            <p>Followers</p>
                                        </div>
                                    </div>

                                    <div class="social-links">
                                        <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                                        <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                        <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                                        <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                    <div class="profile-location mt-4">
                                        <i class="fa fa-map-marker" style="color: #2d465e;"></i> <?= htmlspecialchars($user['location'] ?? "") ?>
                                    </div>
                                    <a href="/profile_edit" class="btn btn-primary mt-4">edit</a>

                                </div>

                            </div>
                        </div>

                        <!-- Author Content -->
                        <div class="col-lg-8">
                            <div class="author-content" data-aos="fade-up" data-aos-delay="200">
                                <div class="content-header">
                                    <h3>About Me</h3>
                                </div>
                                <div class="content-body">
                                    <p> <?= htmlspecialchars($user['bio'] ?? '') ?></p>

                                    <div class="expertise-areas">
                                        <h4>Areas of Expertise</h4>
                                        <div class="tags">
                                            <span>Artificial Intelligence</span>
                                            <span>Cybersecurity</span>
                                            <span>Smart Home Technology</span>
                                            <span>Digital Privacy</span>
                                            <span>Consumer Electronics</span>
                                            <span>Future Tech Trends</span>
                                        </div>
                                    </div>

                                    <div class="featured-articles mt-5">
                                        <h4>Featured Articles</h4>
                                        <div class="row g-4">
                                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                                                <article class="article-card">
                                                    <div class="article-img">
                                                        <img src="public/assets/img/blog/blog-post-10.webp" alt="Article" class="img-fluid">
                                                    </div>
                                                    <div class="article-details">
                                                        <div class="post-category">Technology</div>
                                                        <h5><a>The Future of AI in Everyday Computing</a></h5>
                                                        <div class="post-meta">
                                                            <span><i class="fa fa-clock"></i> Jan 15, 2024</span>
                                                            <span><i class="fa fa-comment"></i> 24 Comments</span>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>

                                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                                                <article class="article-card">
                                                    <div class="article-img">
                                                        <img src="public/assets/img/blog/blog-post-6.webp" alt="Article" class="img-fluid">
                                                    </div>
                                                    <div class="article-details">
                                                        <div class="post-category">Privacy</div>
                                                        <h5><a>Understanding Digital Privacy in 2024</a></h5>
                                                        <div class="post-meta">
                                                            <span><i class="fa fa-clock"></i> Feb 3, 2024</span>
                                                            <span><i class="fa fa-comments"></i> 18 Comments</span>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section><!-- /Author Profile Section -->

    </main>

    <?php require 'Partials/footer.php'; ?>

</body>

</html>