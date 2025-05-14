<body>
    <main class="main">
        <!-- Slider Section -->
        <section id="slider" class="slider section dark-background">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper">

                    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

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

                    <!-- Include Swiper JS -->
                    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

                    <!-- Initialize Swiper -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const swiperConfig = JSON.parse(document.querySelector('.swiper-config').textContent);
                            new Swiper('.init-swiper', swiperConfig);
                        });
                    </script>
                    <?php if ($allPosts > 0) : ?>
                        <div class="swiper-wrapper">
                            <?php for ($x = 0; $x < 5; $x++) : ?>

                                <div class="swiper-slide" style="background-image: url('<?= $allPosts[$x]["image_path"] ?>');">
                                    <div class="content">
                                        <h2><a><?= $allPosts[$x]["caption"] ?></a></h2>
                                        <p style="color: cornsilk;"><?= $allPosts[$x]["name"] ?></p>
                                    </div>
                                </div>
                            <?php endfor; ?>


                        </div>

                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>

                        <div class="swiper-pagination"></div>
                </div>

            </div>

        </section><!-- /Slider Section -->
    <?php endif; ?>


    <div class="container section-title-container d-flex align-items-center justify-content-between">
        <h2>Write something to share...</h2>
    </div>
    <div class="container create-post-card card mb-4">
        <div class="m-3 p-4">
            <form action="" method="post" enctype="multipart/form-data">
                <!-- Category Selector -->
                <label for="">Category</label>
                <div class="mb-3">
                    <select id="category" name="category" class="form-select" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="gaming" <?= ($_POST['category'] ?? '') === 'gaming' ? 'selected' : '' ?>>Gaming</option>
                        <option value="entertainment" <?= ($_POST['category'] ?? '') === 'entertainment' ? 'selected' : '' ?>>Entertainment</option>
                        <option value="sports" <?= ($_POST['category'] ?? '') === 'sports' ? 'selected' : '' ?>>Sports</option>
                        <option value="health" <?= ($_POST['category'] ?? '') === 'health' ? 'selected' : '' ?>>Health</option>
                        <option value="education" <?= ($_POST['category'] ?? '') === 'education' ? 'selected' : '' ?>>Education</option>
                        <option value="Programming" <?= ($_POST['category'] ?? '') === 'Programming' ? 'selected' : '' ?>>Programming</option>
                        <option value="Lifestyle" <?= ($_POST['category'] ?? '') === 'Lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
                        <option value="Tech" <?= ($_POST['category'] ?? '') === 'Tech' ? 'selected' : '' ?>>Tech</option>
                        <option value="Business" <?= ($_POST['category'] ?? '') === 'Business' ? 'selected' : '' ?>>Business</option>
                        <option value="others" <?= ($_POST['category'] ?? 'others') === 'others' ? 'selected' : '' ?>>Others</option>
                    </select>
                </div>

                <!-- Post Content -->
                <div class="mb-3">
                    <label for="">Title</label>
                    <textarea class="form-control post-input" name="caption" id="caption" placeholder="Title" rows="1" maxlength="50" required><?= htmlspecialchars($_POST['caption'] ?? '') ?></textarea>
                </div>

                <label for="">Description</label>
                <div class="mb-3">
                    <textarea class="form-control post-input" name="description" id="description" placeholder="Description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <label for="">Quote</label>
                <div class="mb-3">
                    <textarea class="form-control post-input" name="quote" id="quote" placeholder="quote" rows="3"><?= htmlspecialchars($_POST['quote'] ?? '') ?></textarea>
                </div>

                <!-- Post Actions -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('imageUpload').click()">
                            <i class="fa fa-image"></i> Photo
                        </button>
                        <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary post-button">Post</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Trending Category Section -->
    <section id="trending-category" class="trending-category section">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="container" data-aos="fade-up">
                <div class="row g-5">
                    <div class="col-lg-4">



                        <?php if (!empty($mostLikedPosts) && isset($mostLikedPosts[0])): ?>
                            <div class="post-entry lg">
                                <a><img src="<?= $mostLikedPosts[0]["image_path"] ?? "public/images/download.png" ?>" class="img-fluid"></a>
                                <div class="post-meta"><span class="date"><?= $mostLikedPosts[0]["category"] ?></span> <span class="mx-1">•</span> <span><?= date('M j', strtotime($mostLikedPosts[0]["created_at"])) ?></span>‘</div>
                                <h2><a href="/post/<?= $mostLikedPosts[0]['id'] ?>"><?= htmlspecialchars($mostLikedPosts[0]['caption']) ?></a></h2>

                                <div class="d-flex align-items-center author">
                                    <div class="name">
                                        <h3 class="m-0 p-0"><?= htmlspecialchars($mostLikedPosts[0]['name']) ?></h3>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="col-lg-8">
                        <div class="row g-5">

                            <div class="col-lg-4 border-start custom-border">
                                <div class="post-entry">
                                    <?php for ($x = 1; $x < 5; $x++): ?>
                                        <a><img src="<?= $mostLikedPosts[$x]["image_path"] ?> " alt="" class="img-fluid"></a>
                                        <div class="post-meta"><span class="date"><?= $mostLikedPosts[$x]["category"] ?></span> <span class="mx-1">•</span> <span><?= date('M j', strtotime($mostLikedPosts[$x]["created_at"])) ?></span></div>
                                        <h2><a href="/post/<?= $mostLikedPosts[$x]['id'] ?>"> <?= $mostLikedPosts[$x]["caption"] ?></a></h2>
                                    <?php endfor; ?>

                                </div>

                            </div>





                            <!-- Trending Section -->
                            <?php if (!empty($mostLikedPosts)): ?>
                                <div class="col-lg-4 ms-5">

                                    <div class="trending">
                                        <h3>Trending</h3>
                                        <ul class="trending-post">
                                            <?php foreach ($mostLikedPosts as $index => $post): ?>
                                                <li>
                                                    <a href="/post/<?= $mostLikedPosts[$index]['id'] ?>">
                                                        <span class="number"><?= $index + 1 ?></span>
                                                        <h3><?= htmlspecialchars($post['caption']) ?></h3>
                                                        <span class="author"><?= htmlspecialchars($post['name']) ?></span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                </div> <!-- End Trending Section -->
                            <?php endif; ?>
                        </div>
                    </div>

                </div> <!-- End .row -->
            </div>

        </div>

    </section><!-- /Trending Category Section -->


    </main>



</body>

</html>