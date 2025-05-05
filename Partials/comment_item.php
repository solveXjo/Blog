<?php
if (!function_exists('displayComment')) {
    function displayComment($comment, $postId, $userId, $depth = 0)
    {
        $maxDepth = 5;
        $marginLeft = $depth > 0 ? min($depth * 40, 200) : 0;
?>
        <div id="comment-<?= $comment['id'] ?>" class="comment" style="margin-left: <?= $marginLeft ?>px">
            <div class="d-flex gap-3">
                <div class="comment-img">
                    <img src="<?= !empty($comment['image_path']) ? 'uploads/' . htmlspecialchars($comment['image_path']) : 'assets/img/default-profile.png' ?>"
                        class="rounded-circle">
                </div>
                <div class="comment-info flex-grow-1 position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($comment['name']) ?></h5>
                            <small class="comment-time">
                                <i class="far fa-clock me-1"></i>
                                <?= date("M j, Y g:i a", strtotime($comment['created_at'])) ?>
                            </small>
                        </div>
                        <?php if ($userId === $comment['user_id']) : ?>
                            <div class="dropdown comment-actions">
                                <button class="btn btn-sm btn-link text-muted dropdown-toggle" type="button"
                                    id="dropdownMenuButton-<?= $comment['id'] ?>"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    data-bs-auto-close="true">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-<?= $comment['id'] ?>">
                                    <li>
                                        <button class="dropdown-item" onclick="showEditForm(<?= $comment['id'] ?>)">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form method="POST" action="/comment/<?= $postId ?>">
                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                            <button type="submit" name="delete_comment" class="dropdown-item text-danger"
                                                onclick="return confirm('Are you sure you want to delete this comment?');">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="comment-content mt-2">
                        <p class="mb-2"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>

                        <?php if ($depth < $maxDepth) : ?>
                            <button class="reply-btn" onclick="toggleReplyForm(<?= $comment['id'] ?>)">
                                <i class="fas fa-reply me-1"></i>Reply
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Edit form -->
                    <div class="edit-form mt-2">
                        <form method="POST" action="/comment/<?= $postId ?>" class="d-flex gap-2">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <textarea name="new_comment" class="form-control" rows="3" required><?= htmlspecialchars($comment['comment']) ?></textarea>
                            <div class="d-flex flex-column gap-2">
                                <button type="submit" name="edit_comment" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="hideEditForm(<?= $comment['id'] ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reply form -->
            <?php if ($depth < $maxDepth) : ?>
                <div id="reply-form-<?= $comment['id'] ?>" class="reply-form mt-3 ms-5">
                    <form method="POST" action="/comment/<?= $postId ?>" class="d-flex gap-3">
                        <div>
                            <img src="<?= 'uploads/' . ($userInfo['image_path'] ?? 'default.png') ?>"
                                alt="Your profile"
                                class="rounded-circle"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                            <textarea name="reply" class="form-control mb-2" rows="2"
                                placeholder="Write your reply..." required></textarea>
                            <div class="d-flex gap-2">
                                <button type="submit" name="submit_reply" class="btn btn-sm btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Post Reply
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="toggleReplyForm(<?= $comment['id'] ?>)">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Recursive replies -->
            <?php if (!empty($comment['replies'])) : ?>
                <div class="replies-container mt-3">
                    <?php foreach ($comment['replies'] as $reply) : ?>
                        <?php displayComment($reply, $postId, $userId, $depth + 1); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
    <?php
    }
}

displayComment($comment, $postId, $userId);
    ?>