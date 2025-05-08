<head>
    <?php include "src/Partials/head.php";
    require "src/app/controllers/team.php"

    ?>

</head>

<body>
    <section id="team" class="team section light-background mt-4 p-4">



        <div class="container section-title" data-aos="fade-up">
            <h2>Team</h2>
            <div><span>Check Our</span> <span class="description-title">Team</span></div>
        </div>


        <div class="team-container">
            <?php if (!empty($teamMembers)): ?>
                <?php foreach ($teamMembers as $member): ?>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="team-member d-flex mb-3">



                            <img src="src/public/assets/img/person/<?= htmlspecialchars($member['image']) ?>"
                                class="img-fluid" alt="<?= htmlspecialchars($member['name']) ?>" style="width: 200;">
                            <div class="member-info flex-grow-1">
                                <h4><?= htmlspecialchars($member['name']) ?></h4>
                                <span><?= htmlspecialchars($member['title']) ?></span>
                                <p><?= htmlspecialchars($member['about'] ?? 'Professional team member') ?></p>
                                <div class="social">
                                    <a href="#"><i class="fa fa-envelope"></i></a>
                                    <a href="#"><i class="fab fa-linkedin"></i></a>
                                    <a href="#"><i class="fab fa-github"></i></a>
                                    <a href=""><i class="fab fa-youtube"></i></a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No team members found.</div>
            <?php endif; ?>
        </div>


    </section>

</body>