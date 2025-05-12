<body class="contact-page">

    <main class="main">
        <div class="title-wrapper mt-3">
            <h1>Contact</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>
        </div>

        <?php
        echo $contactController->getSuccessMessage();
        echo $contactController->getErrorMessage();
        ?>

        <!-- Contact Section -->
        <section id="contact" class="contact section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4 mb-5">
                    <!-- Contact info cards... -->
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-wrapper" data-aos="fade-up" data-aos-delay="400">
                            <form action="/contact" method="POST" role="form" class="php-email-form">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                            <input type="text" name="name" class="form-control" placeholder="Your name*" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                            <input type="email" class="form-control" name="email" placeholder="Email address*" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                            <input type="text" class="form-control" name="phone" placeholder="Phone number*" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-list"></i></span>
                                            <select name="subject" class="form-control" required>
                                                <option value="" disabled selected>Select service*</option>
                                                <option value="Consulting" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Consulting') ? 'selected' : ''; ?>>Consulting</option>
                                                <option value="Development" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Development') ? 'selected' : ''; ?>>Development</option>
                                                <option value="Marketing" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                                                <option value="Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Support') ? 'selected' : ''; ?>>Support</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-comments"></i></span>
                                            <textarea class="form-control" name="message" rows="6" placeholder="Write a message*" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="my-3">
                                        <div class="loading">Loading</div>
                                        <div class="error-message"></div>
                                        <div class="sent-message">Your message has been sent. Thank you!</div>
                                    </div>
                                    <div class="g-recaptcha my-3" data-sitekey="<?php echo $contactController->getSiteKey(); ?>"></div>
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Submit Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Google Map -->
        <section id="map" class="map section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3384.5641636554733!2d35.83622922338131!3d31.972718574010102!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151ca19fc642c40b%3A0xf1cfbb98909c02f9!2sAltibbi!5e0!3m2!1sar!2sjo!4v1744718692313!5m2!1sar!2sjo"
                    width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
    </main>

</body>

</html>