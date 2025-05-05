<?php
session_start();
require 'app/controllers/Posts.php';
require 'vendor/autoload.php';

$parser = new Parsedown();
?>

<title>Posts</title>

<head>
  <?php include 'Partials/head.php'; ?>
  <title>SocialApp</title>
  <script>
    $(document).ready(function() {
      $('.like-btn').click(function() {
        var $button = $(this);
        var postId = $button.data('post-id');
        var $icon = $button.find('i');
        var isLiked = $icon.hasClass('liked');
        var $likeCount = $button.closest('.post-actions').prev().find('.like-count');
        var currentLikes = parseInt($likeCount.text());

        $likeCount.text(isLiked ? currentLikes - 1 : currentLikes + 1);
        $icon.toggleClass('liked');
        $button.prop('disabled', true);

        $.post('app/controllers/handle_like.php', {
          post_id: postId,
          action: isLiked ? 'unlike' : 'like'
        }, function(response) {
          $likeCount.text(response);
        }).always(function() {
          $button.prop('disabled', false);
        });
      });
    });
  </script>
</head>


<body>
  <?php include 'Partials/nav.php'; ?>
  <?php include 'Partials/pageTitle.php'; ?>




  <main class="main">
    <div class="container">
      <div class="row">

        <div class="col-lg-8">


          <section id="blog-details" class="blog-details section">
            <div class="container">
              <?php if (count($posts) > 0) : ?>
                <?php foreach ($posts as $post) : ?>
                  <article class="article p-5 mb-3">
                    <?php if (!empty($post['image_path'])) : ?>
                      <div class="post-img">
                        <a href="/post/<?= $post['id'] ?>-<?= createSlug($post['caption']) ?>">
                          <img src="<?= $post['image_path'] ?>" alt="Post image" class="img-fluid" margin-left: 15;>
                        </a>
                      </div>
                    <?php endif; ?>

                    <h2 class="title">
                      <a class="single-post-col" href="/post/<?= $post['id'] ?>-<?= createSlug($post['caption']) ?>">
                        <?= $parser->text($post['caption']) ?>
                      </a>
                    </h2>

                    <div class="meta-top">
                      <ul>
                        <li class="d-flex align-items-center">
                          <i class="fa fa-user"></i>
                          <a><?= htmlspecialchars($post['name']) ?></a>
                        </li>
                        <li class="d-flex align-items-center">
                          <i class="fa fa-clock"></i>
                          <a><time datetime="<?= date('Y-m-d', strtotime($post['created_at'])) ?>">
                              <?= date('F j, Y', strtotime($post['created_at'])) ?>
                            </time></a>
                        </li>
                        <li class="d-flex align-items-center">
                          <i class="fa fa-thumbs-up"></i>
                          <a><span class="like-count"><?= $post['likes'] ?></span> Likes</a>
                        </li>
                      </ul>
                    </div>

                    <!-- Post actions with your working like functionality -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                      <div class="post-actions" style="margin-top: 20px;">
                        <button type="button" class="btn btn-outline-primary action-btn like-btn" data-post-id="<?= $post['id'] ?>">
                          <i class="fa fa-thumbs-up <?= $post['liked'] ? 'liked' : '' ?>"></i> Like
                        </button>
                        <a href="/comment/<?= $post['id'] ?>" class="btn btn-outline-secondary">
                          <i class="fa fa-comment"></i> Comment
                        </a>

                        <?php if ($post['user_id'] == ($_SESSION['user_id'] ?? null)) : ?>
                          <form method="post" action="/posts" style="margin: 0;">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <button type="submit" name="delete_post" class="btn btn-outline-danger"
                              onclick="return confirm('Are you sure you want to delete this post?');">
                              <i class="fa fa-trash"></i> Delete
                            </button>
                          </form>
                        <?php endif; ?>
                      </div>

                      <!-- Comments Section -->
                      <div class="comments-section mt-4">
                        <?php if (!empty($post['comments'])) : ?>
                          <h4>Comments</h4>
                          <?php foreach ($post['comments'] as $comment) : ?>
                            <div class="card mb-2">
                              <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                  <?= htmlspecialchars($comment['name']) ?>
                                  <small class="text-muted"><?= date('M j, Y', strtotime($comment['created_at'])) ?></small>
                                </h6>
                                <p class="card-text"><?= htmlspecialchars($comment['text']) ?></p>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        <?php endif; ?>

                        <form method="post" action="/comment/<?= $post['id'] ?>" class="mt-3">
                          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                          <div class="input-group">
                            <textarea name="comment" class="form-control" placeholder="Write a comment..." rows="1" required></textarea>
                            <button type="submit" name="submit" class="btn btn-primary">Post</button>
                          </div>
                        </form>
                      </div>
                    <?php endif; ?>
                  </article>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="no-posts">
                  <i class="fa fa-newspaper-o" style="font-size: 60px; margin-bottom: 15px;"></i>
                  <h3>No posts yet</h3>
                  <p>When you or your friends share something, it will appear here.</p>
                </div>
              <?php endif; ?>
            </div>
          </section>



        </div>

        <div class="col-lg-4 sidebar">

          <div class="widgets-container">



            <!-- Recent Posts Widget -->
            <div class="recent-posts-widget widget-item mt-5">
              <h3 class="widget-title">Recent Posts</h3>
              <?php foreach ($getMostRecentPosts as $recent) : ?>
                <div class="post-item d-flex align-items-center mb-3">
                  <?php if (!empty($recent['image_path'])) : ?>
                    <img src="<?= htmlspecialchars($recent['image_path']) ?>" alt="Recent post image" class="flex-shrink-0 rounded">
                  <?php endif; ?>
                  <div>
                    <h4 class="mb-1">
                      <a><?= htmlspecialchars($recent["caption"]) ?></a>
                    </h4>
                    <time datetime="<?= htmlspecialchars($recent["created_at"]) ?>">
                      <?= date('M d, y', strtotime($recent["created_at"])) ?>
                    </time>
                  </div>
                </div><!-- End recent post item -->
              <?php endforeach; ?>
            </div><!--/Recent Posts Widget -->

            <!-- Tags Widget -->
            <div class="tags-widget widget-item mt-5">

              <h3 class="widget-title">Tags</h3>
              <ul>
                <li><a>App</a></li>
                <li><a>IT</a></li>
                <li><a>Business</a></li>
                <li><a>Mac</a></li>
                <li><a>Design</a></li>
                <li><a>Office</a></li>
                <li><a>Creative</a></li>
                <li><a>Studio</a></li>
                <li><a>Smart</a></li>
                <li><a>Tips</a></li>
                <li><a>Marketing</a></li>
              </ul>

            </div><!--/Tags Widget -->

          </div>

        </div>

      </div>
    </div>

  </main>

  <?php require 'Partials/footer.php'; ?>

</body>

</html>