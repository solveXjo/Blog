<?php
require_once 'src/app/controllers/signup.php';
?>



<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="signup-card">
                    <div class="row no-gutters">
                        <div class="col-lg-7 form-section">
                            <div class="form-header">
                                <img src="src/public/images/download.png" alt="Sign up icon">
                                <h4>Create Your Account</h4>
                            </div>

                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($name) ?>" required>
                                    <span class="error"><?= $nameErr ?></span>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" value="<?= htmlspecialchars($age) ?>" required>
                                    <span class="error"><?= $ageErr ?></span>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" required>
                                    <span class="error"><?= $emailErr ?></span>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                    <span class="error"><?= $passErr ?></span>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control" name="fileToUpload">
                                    <span class="error"><?= $imageErr ?></span>
                                </div>

                                <div class="d-grid gap-2 mt-5">
                                    <button type="submit" name="submit" class="btn btn-primary">Sign Up</button>
                                </div>

                                <div class="d-flex align-items-center justify-content-center pb-4 mt-4">
                                    <p class="mb-0 me-2">Already have an account?</p>
                                    <a href="/Login" class="btn btn-outline-danger  ">Login</a>
                                </div>
                            </form>
                        </div>

                        <div class="col-lg-5 graphic-section">
                            <div>
                                <h3 class="text-light">Join Our Community</h3>
                                <p class="mt-4 text-white">Create an account to unlock all features and connect with like-minded people. We're excited to have you on board!</p>
                                <ul class="mt-4" style="list-style-type: none; padding-left: 0;">
                                    <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Personalized dashboard</li>
                                    <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Secure data storage</li>
                                    <li class="mb-3"><i class="fas fa-check-circle me-2"></i> 24/7 customer support</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>