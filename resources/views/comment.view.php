<?php
require "app/controllers/comment.php";
?>


<head>
    <?php include 'Partials/head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>


    <title>Comments</title>
    <style>
        .comment {
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .comment-img img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-info {
            flex-grow: 1;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            position: relative;
        }

        .comment-info:before {
            content: '';
            position: absolute;
            left: -10px;
            top: 15px;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 10px solid #f8f9fa;
        }

        .replies-container {
            margin-left: 60px;
            border-left: 2px solid #dee2e6;
            padding-left: 20px;
        }

        .reply-form,
        .edit-form {
            display: none;
            margin-top: 1rem;
        }

        .comment-actions {
            position: absolute;
            right: 10px;
            top: 10px;
        }

        .comment-content {
            margin-top: 0.5rem;
        }

        .comment-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .reply-btn {
            background: none;
            border: none;
            color: #0d6efd;
            padding: 0;
            font-size: 0.9rem;
        }

        .reply-btn:hover {
            text-decoration: underline;
        }

        .dropdown-toggle::after {
            display: none;
        }
    </style>

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