<!DOCTYPE html>
<html lang="en">

<head>

    <title>404 - Blogy Bootstrap Template</title>
    <?php require_once 'Partials/head.php'; ?>


</head>

<body class="page-404">
    <?php include 'Partials/nav.php'; ?>
    <main class="main">

        <!-- Error 404 Section -->
        <section id="error-404" class="error-404 section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="text-center">
                    <div class="error-icon mb-4" data-aos="zoom-in" data-aos-delay="200">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>

                    <h1 class="error-code mb-4" data-aos="fade-up" data-aos-delay="300">404</h1>

                    <h2 class="error-title mb-3" data-aos="fade-up" data-aos-delay="400">Oops! Page Not Found</h2>

                    <p class="error-text mb-4" data-aos="fade-up" data-aos-delay="500">
                        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
                    </p>

                    <div class="error-action" data-aos="fade-up" data-aos-delay="700">
                        <a href="/" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>

            </div>

        </section><!-- /Error 404 Section -->

    </main>

    <?php include 'Partials/footer.php'; ?>



</body>

</html>