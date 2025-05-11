<?php
// require "controllers/Posts.php";
require 'vendor/autoload.php';

$config = require "src/config/config.php";

$db = new App\Core\Database($config);
$postRepo = new App\Models\PostRepository($db);
$userRepo = new App\Models\UserRepository($db);
$SP = new App\Controllers\SinglePostController();

$parser = new Parsedown();

$postId = $SP->getPostId();
$singlepost = $postRepo->getPostById($postId);

$userId = $SP->getUserId($singlepost);

$userInfo = $userRepo->getUserById($userId);
?>

<head>
    <title>Single Post</title>

    <?php include "src/Partials/head.php"; ?>
    <script type="application/json" class="swiper-config">
        {
            "loop": true,
            "speed": 600,
            "autoplay": {
                "delay": 5000
            },
            "slidesPerView": "auto",
            "centeredSlides": true,
            "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
            },
            "navigation": {
                "nextEl": ".swiper-button-next",
                "prevEl": ".swiper-button-prev"
            }
        }
    </script>
</head>


<?php
$previous = "javascript:history.go(-1)";
if (isset($_SERVER['HTTP_REFERER'])) {
    $previous = $_SERVER['HTTP_REFERER'];
}
?>

<body class="gallery-single-page">
    <?php include "src/Partials/nav.php"; ?>
    <main class="main">
        <section id="gallery-details" class="gallery-details section">

            <div class="container" data-aos="fade-up">
                <div style="margin-bottom: 20px;">
                    <a href="<?= $previous ?>"><i class='fas fa-angle-left' style='font-size:18px'>Back</i></a>

                </div>
                <div class="d-flex align-items-center justify-content-center">
                    <!-- Page Title -->


                    <div class="row justify-content-between">
                        <div class="page-title" data-aos="fade">
                            <img src="<?= $singlepost["image_path"] ? 'src/' . $singlepost["image_path"] : '' ?>" style="max-height: 500px;">
                            <div class="heading">
                                <div class="row d-flex justify-content-center text-center">
                                    <div class="col-lg-8">
                                        <h2><?= $parser->text($singlepost["caption"]) ?></h2>
                                        <!-- <p class="mb-0"><?= $singlepost["description"] ?></p> -->
                                        <!-- <a href="contact.html" class="cta-btn">Available for Hire<br></a> -->
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Page Title -->


                        <div class="col-lg-8" data-aos="fade-up">
                            <div class="portfolio-description">
                                <h2>Description</h2>
                                <?php if (isset($singlepost["description"])): ?>
                                    <p>
                                        <?= $singlepost["description"] ?>
                                    </p>
                                <?php else: ?>
                                    <p>
                                        No description available.
                                    </p>

                                <?php endif; ?>

                                <?php if (isset($singlepost["quote"])): ?>

                                    <div class="testimonial-item">
                                        <p>
                                            <i class="bi bi-quote quote-icon-left"></i>
                                            <span> <?= $singlepost["quote"] ?> </span>
                                            <i class="bi bi-quote quote-icon-right"></i>
                                        </p>
                                        <div>

                                            <img src="<?= "uploads/" . $userInfo["image_path"] ?? 'images/download.png' ?>" class="testimonial-img" alt="">
                                            <h3><?= $userInfo["name"] ?></h3>
                                            <h4><?= $userInfo["title"] ?></h4>
                                        </div>
                                    </div>
                                <?php endif; ?>


                            </div>
                        </div>
                        <div class="col-lg-3" data-aos="fade-up" data-aos-delay="100">
                            <div class="portfolio-info">
                                <h3>Post information</h3>
                                <ul>
                                    <li><strong>Created By</strong> <?= $userInfo["name"] ?></li>
                                    <li><strong>Location</strong> <?= $userInfo["location"] ?? "unknown" ?></li>

                                    <li><strong>Category</strong> <?= $singlepost["category"] ?></li>
                                    <li><strong>Created at</strong> <?= date("d/M/Y - D", strtotime($singlepost["created_at"])) ?></li>
                                    <!-- <li><strong>Project URL</strong> <a href="#">www.example.com</a></li> -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </section>

    </main>

    <?php include "src/Partials/footer.php"; ?>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="fa fa-arrow-up-short"></i></a>


    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="src/assets/js/main.js"></script>

</body>

</html>