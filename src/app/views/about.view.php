<head>
  <title>About</title>
</head>

<body>
  <?php include 'src/Partials/nav.php'; ?>
  <?php include 'src/Partials/pageTitle.php'; ?>
  <!-- About Section -->
  <section id="about" class="about section">

    <div class="container">

      <div class="row gy-4">

        <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
          <p class="who-we-are">Who We Are</p>
          <h3>Unleashing Potential with Creative Strategy</h3>
          <p class="fst-italic">
            Hey, I’m Fathi Al Sadi—competitive programmer, problem-solver, and your guide to thinking like a champion.

            I’ve battled on JCPC and Codeforces, and now I break down the strategies behind winning at coding (and life). Expect hard-earned insights, zero fluff, and tactics that work.

            Want to outthink, outcode, and outperform? Let’s go.


          </p>
          <ul>
            <li><i class="bi bi-check-circle"></i> <span>🔥 "Steal My Battle-Tested Strategies – Learn the exact problem-solving frameworks I used to dominate JCPC & Codeforces leaderboards.".</span></li>
            <li><i class="bi bi-check-circle"></i> <span>🚀 "Coding Hacks Meets Life Hacks – Discover how competitive programming tactics can crush real-world challenges (yes, even your toughest goals)."</span></li>
            <li><i class="bi bi-check-circle"></i> <span>💡 "No Fluff, Just Results – Straight-to-the-point insights that help you think sharper – faster than any ‘motivational’ guru ever could."</span></li>
          </ul>
        </div>

        <div class="col-lg-6 about-images" data-aos="fade-up" data-aos-delay="200">
          <div class="row gy-4">
            <div class="col-lg-6">
              <img src="src/public/assets/img/about-company-1.jpg" class="img-fluid" alt="">
            </div>
            <div class="col-lg-6">
              <div class="row gy-4">
                <div class="col-lg-12">
                  <img src="src/public/assets/img/about-company-2.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-lg-12">
                  <img src="src/public/assets/img/about-company-3.jpg" class="img-fluid" alt="">
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

      <?php include 'src/app/views/team.view.php'; ?>

    </div>
  </section><!-- /About Section -->


  <?php include 'src/Partials/footer.php'; ?>
</body>

</html>