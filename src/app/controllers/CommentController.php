<?php


namespace App\Controllers;

use App\Core\BaseController;
use App\Models\PostRepository;
use App\Models\UserRepository;

use App\Core\Route;

class CommentController extends BaseController
{
    protected $postRepo;
    protected $userRepo;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->postRepo = new PostRepository($this->db);
        $this->userRepo = new UserRepository($this->db);
    }

    public function show()
    {
        $commentController = new CommentController();
        $commentController->processRequest();

        $postId = $commentController->getPostId();
        $userId = $commentController->getUserId();
        $userInfo = $commentController->getUserInfo($userId);
        $comments = $commentController->getAllComments($postId);
        $commentCount = $commentController->getCommentCount($comments);

        echo $this->view->renderWithLayout('comment.view.php', 'layouts/main.php', [
            "title" => "Comment -Altibbi",
            'postData' => $this->postData,
            'postId' => $postId,
            'userId' => $userId,
            'userInfo' => $userInfo,
            'comments' => $comments,
            'commentCount' => $commentCount
        ]);
    }

    // #[Route('/comment', 'GET')]

    public function getUserId()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /index");
            exit();
        }

        $userId = $_SESSION['user_id'];
        if (empty($userId) || !is_numeric($userId)) {
            die("Invalid user ID.");
        }
        return $userId;
    }

    public function getPostId()
    {
        $postId = $_GET['post_id'] ?? null;
        if (empty($postId) || !is_numeric($postId)) {
            die("Invalid post ID.");
        }
        return $postId;
    }

    public function getPostDetails($postId)
    {
        $postDetails = $this->postRepo->getPostById($postId);
        if (!$postDetails) {
            die("Post not found.");
        }
        return $postDetails;
    }

    public function getCaption($postDetails)
    {
        $caption = $postDetails['caption'] ?? '';
        if (strlen($caption) > 50) {
            $caption = substr($caption, 0, 50) . '...';
        }
        return $caption;
    }

    public function getCommentText()
    {
        $commentText = $_POST['comment'] ?? '';
        if (empty(trim($commentText))) {
            die("Comment text is required.");
        }
        return $commentText;
    }

    public function getParentId()
    {
        $parentId = $_POST['parent_id'] ?? null;
        if ($parentId && !is_numeric($parentId)) {
            die("Invalid parent ID.");
        }
        return $parentId;
    }

    public function addComment($postId, $userId, $commentText, $parentId = null)
    {
        if ($postId && !empty($commentText)) {
            $this->postRepo->addComment($postId, $userId, $commentText, $parentId);
            header("Location: /comment/" . $postId);
            exit();
        }
    }

    public function replySubmission()
    {
        $postId = $this->getPostId();
        $userId = $this->getUserId();
        $commentText = $_POST['reply'] ?? '';
        $parentId = $_POST['parent_id'] ?? null;

        if ($postId && $parentId && !empty($commentText)) {
            $this->postRepo->addComment($postId, $userId, $commentText, $parentId);
            header("Location: /comment/" . $postId);
            exit();
        }
    }

    public function getCommentId()
    {
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
        if ($commentId === false || $commentId === null) {
            return null;
        }
        return $commentId;
    }

    public function newCommentText()
    {
        $newText = trim($_POST['new_comment'] ?? '');
        if (empty($newText)) {
            die("New comment text is required.");
        }
        return $newText;
    }

    public function editComment()
    {
        $commentId = $this->getCommentId();
        $newText = $this->newCommentText();
        $postId = $this->getPostId();

        if ($commentId && !empty($newText)) {
            $this->postRepo->updateComment($commentId, $newText);
            header("Location: /comment/" . $postId);
            exit();
        }
    }

    public function deleteComment()
    {
        $commentId = $this->getCommentId();
        $postId = $this->getPostId();

        if ($commentId !== null) {
            $this->postRepo->deleteComment($commentId);
            header("Location: /comment/" . $postId);
            exit();
        }
    }

    public function getAllComments($postId)
    {
        return $this->postRepo->getAllComments($postId);
    }

    public function getCommentCount($comments)
    {
        return is_array($comments) ? count($comments) : 0;
    }

    public function getUserInfo($userId)
    {
        return $this->userRepo->getUserById($userId);
    }

    public function processRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle comment submission
            if (isset($_POST['comment'], $_POST['post_id'])) {
                $postId = $_POST['post_id'];
                $userId = $this->getUserId();
                $commentText = $_POST['comment'];
                $parentId = $_POST['parent_id'] ?? null;
                $this->addComment($postId, $userId, $commentText, $parentId);
            }

            // Handle reply submission
            if (isset($_POST['submit_reply'])) {
                $this->replySubmission();
            }

            // Handle comment edit
            if (isset($_POST['edit_comment'])) {
                $this->editComment();
            }

            // Handle comment deletion
            if (isset($_POST['delete_comment'])) {
                $this->deleteComment();
            }
        }
    }
}
