<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'src/Partials/head.php'; ?>
    <title><?= $title ?? 'Default Title' ?></title>
</head>

<body class="contact-page">
    <?php include 'src/Partials/nav.php'; ?>
    <?php include 'src/Partials/pageTitle.php'; ?>

    <main class="main">
        <?= $content ?? '' ?>
    </main>

    <?php include 'src/Partials/footer.php'; ?>
</body>

</html>