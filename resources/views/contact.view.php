<?php
require 'vendor/autoload.php';
require 'app/controllers/ContactController.php';

// Initialize the controller (assuming you have a Database class)
require_once 'app/core/Database.php';
$db = new Database(require 'config/config.php');
$contactController = new ContactController($db);
$contactController->handleContactForm();
?>

<head>
    <?php include "Partials/head.php"; ?>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Contact - Altibbi</title>
    <style>
        /*--------------------------------------------------------------
# Contact Section
--------------------------------------------------------------*/
        .contact .info-card {
            background-color: var(--surface-color);
            padding: 30px;
            text-align: center;
            height: 100%;
            border-radius: 10px;
            border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%);
            transition: all 0.3s ease-in-out;
        }

        .contact .info-card:hover {
            transform: translateY(-5px);
        }

        .contact .info-card .icon-box {
            width: 56px;
            height: 56px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: color-mix(in srgb, var(--accent-color), transparent 92%);
        }

        .contact .info-card .icon-box i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .contact .info-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .contact .info-card p {
            margin: 0;
            color: color-mix(in srgb, var(--default-color), transparent 20%);
            font-size: 15px;
            line-height: 1.6;
        }

        .contact .form-wrapper .input-group .input-group-text {
            color: var(--accent-color);
            background-color: color-mix(in srgb, var(--default-color), transparent 96%);
            border-color: color-mix(in srgb, var(--default-color), transparent 85%);
            border-radius: 8px 0 0 8px;
            padding: 12px 15px;
        }

        .contact .form-wrapper .input-group .form-control {
            color: var(--default-color);
            background-color: var(--surface-color);
            border-radius: 0 8px 8px 0;
            box-shadow: none;
            font-size: 14px;
            border-color: color-mix(in srgb, var(--default-color), transparent 85%);
            padding: 12px 15px;
        }

        .contact .form-wrapper .input-group .form-control:focus {
            border-color: var(--accent-color);
        }

        .contact .form-wrapper .input-group .form-control::placeholder {
            color: color-mix(in srgb, var(--default-color), transparent 70%);
        }

        .contact .form-wrapper select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 40px;
        }

        .contact .form-wrapper textarea.form-control {
            min-height: 160px;
        }

        .contact .form-wrapper button {
            background-color: var(--accent-color);
            border: 0;
            padding: 12px 40px;
            color: var(--contrast-color);
            border-radius: 50px;
            transition: 0.3s;
            font-size: 16px;
            font-weight: 500;
        }

        .contact .form-wrapper button:hover {
            background-color: color-mix(in srgb, var(--accent-color), transparent 20%);
        }
    </style>
</head>

<body class="contact-page">
    <?php
    include "Partials/nav.php";
    include "Partials/pageTitle.php";
    ?>

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

    <?php include "Partials/footer.php"; ?>
</body>

</html>