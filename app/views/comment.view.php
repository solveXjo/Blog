<?php
require 'vendor/autoload.php';
require_once 'app/controllers/CommentController.php';

use App\Core\Database;

use App\Models\PostRepository;
use App\Models\UserRepository;

use App\Controllers\CommentController;


$config = require 'config/config.php';
$db = new Database($config);
$postRepo = new PostRepository($db);
$userRepo = new UserRepository($db);
$commentController = new CommentController($db, $postRepo, $userRepo);

$commentController->processRequest();

$postId = $commentController->getPostId();
$userId = $commentController->getUserId();
$userInfo = $commentController->getUserInfo($userId);
$comments = $commentController->getAllComments($postId);
$commentCount = $commentController->getCommentCount($comments);

?>

<head>
    <?php include 'Partials/head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Comments</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElements = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElements.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            document.addEventListener('click', function(event) {
                if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-menu')) {
                    var dropdowns = [].slice.call(document.querySelectorAll('.dropdown-menu.show'));
                    dropdowns.forEach(function(openDropdown) {
                        var dropdown = bootstrap.Dropdown.getInstance(openDropdown.previousElementSibling);
                        if (dropdown) {
                            dropdown.hide();
                        }
                    });
                }
            });
        });
    </script>
</head>

<body class='background'>
    <?php require 'Partials/nav.php'; ?>

    <div class="container py-5">
        <a href="/posts" class="btn btn-outline-secondary mb-4">
            <i class="fas fa-arrow-left me-2"></i>Back to Posts
        </a>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0"><?= $commentCount ?> Comments</h4>
            </div>
            <div class="card-body">
                <!-- Main comment form -->
                <form method="POST" action="/comment/<?= $postId ?>" class="mb-4">
                    <div class="d-flex gap-3">
                        <div>
                            <img src="<?= 'uploads/' . ($userInfo['image_path'] ?? 'default-profile.png') ?>"
                                alt="Your profile"
                                class="rounded-circle"
                                style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <textarea name="comment" class="form-control mb-2" rows="3"
                                placeholder="Write your comment..." required></textarea>
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </div>
                    </div>
                </form>

                <div class="comments-list mt-4">
                    <?php if (!empty($comments)) : ?>
                        <?php foreach ($comments as $comment) : ?>
                            <?php include 'Partials/comment_item.php'; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="text-center py-4">
                            <i class="far fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No comments yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';

            if (form.style.display === 'block') {
                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
                form.querySelector('textarea').focus();
            }
        }

        function showEditForm(commentId) {
            document.querySelector(`#comment-${commentId} .edit-form`).style.display = 'block';
            document.querySelector(`#comment-${commentId} .comment-content`).style.display = 'none';
            document.querySelector(`#comment-${commentId} .edit-form textarea`).focus();
        }

        function hideEditForm(commentId) {
            document.querySelector(`#comment-${commentId} .edit-form`).style.display = 'none';
            document.querySelector(`#comment-${commentId} .comment-content`).style.display = 'block';
        }
    </script>
</body>

</html>