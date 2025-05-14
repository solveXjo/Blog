<?php
require_once 'src/app/controllers/Login.php';
include 'src/Partials/head.php';
?>


<body>
  <div class="container py-5">
    <div class="row d-flex justify-content-center">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <img src="src/public/images/download.png"
                    style="width: 185px;" alt="logo">
                  <h4 class="mt-1 mb-5 pb-1">Welcome to Our Platform</h4>
                </div>

                <form action="" method="post">
                  <span class="error"><?php echo $invalid; ?></span>

                  <div data-mdb-input-init class="form-outline mb-4">
                    <label class="form-label" for="form2Example11">Email</label>
                    <input type="email" id="form2Example11" class="form-control" name="email" placeholder="Enter email" required />
                  </div>

                  <div data-mdb-input-init class="form-outline mb-4">
                    <label class="form-label" for="form2Example22">Password</label>
                    <input type="password" id="form2Example22" class="form-control" name="password" placeholder="Enter password" required />
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                    <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block fa-lg mb-3" type="submit">Log in</button>
                    <!-- <a class="text-muted" href="#!">Forgot password?</a> -->
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Don't have an account?</p>
                    <a href="/signup" class="btn btn-outline-danger">Sign up</a>
                  </div>

                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4 about-login">
                <h4 class="mb-4 head1">We are more than just a company</h4>
                <p class="small mb-0 text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Necessitatibus ipsam inventore, dignissimos officia itaque deserunt nihil explicabo saepe vitae consectetur fugit laudantium architecto voluptates deleniti odio totam quae sint amet.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>