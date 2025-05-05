<?php

function displayComments($comments, $parentId = null, $level = 0) {
    global $postRepo;

    $filteredComments = array_filter($comments, fn($c) => $c['parent_comment_id'] == $parentId);

    foreach ($filteredComments as $comment) {
        ?>
        <div class="ms-<?= $level * 3 ?> mt-2">
            <p><strong><?= htmlspecialchars($comment['name']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
            <small><?= htmlspecialchars($comment['created_at']) ?></small>

            <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <button type="submit" name="delete_comment" class="btn btn-danger btn-sm ms-2">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>

                <form method="post" action="" style="display:inline;">
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <textarea name="new_comment"><?= htmlspecialchars($comment['comment']) ?></textarea>
                    <button type="submit" name="edit_comment" class="btn btn-success btn-sm ms-2">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                </form>
            <?php endif; ?>

            <form method="post" action="" class="mt-2">
                <input type="hidden" name="post_id" value="<?= $comment['post_id'] ?>">
                <input type="hidden" name="parent_comment_id" value="<?= $comment['id'] ?>">
                <textarea name="comment" placeholder="Write a reply..."></textarea>
                <button type="submit" name="add_comment" class="btn btn-primary btn-sm">
                    <i class="fa fa-reply"></i> Reply
                </button>
            </form>

            <?php displayComments($comments, $comment['id'], $level + 1); ?>
        </div>
        <?php
    }
}