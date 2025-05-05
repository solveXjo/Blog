<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'Partials/head.php' ?>

</head>

<body>



  <header id="header" class="header position-relative">
    <div class="container-fluid container-xl position-relative">

      <div class="top-row d-flex align-items-center justify-content-between">
        <a href="/home" class="logo d-flex align-items-end">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="assets/img/logo.webp" alt=""> -->
          <h1 class="sitename">Altibbi</h1><span>.</span>
        </a>

        <div class="d-flex align-items-center">
          <div class="social-links">
            <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
          </div>


        </div>
      </div>

    </div>

    <div class="nav-wrap">
      <div class="container d-flex justify-content-center position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="/home" class="active">Home</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/category">Category</a></li>
            <li><a href="/posts">Posts</a></li>
            <li><a href="/profile">Profile</a></li>

            <li><a href="/contact">Contact</a></li>
            <!-- <li><a href="single-post.view.php">single-post</a></li> -->
            <li><a style="color: red; margin-left:50px;" href="/signup">Logout</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none fa fa-list"></i>
        </nav>
      </div>
    </div>

  </header>

</body>

</html>