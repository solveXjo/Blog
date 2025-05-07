<?php
require_once 'app/controllers/Posts.php';
$userRepo = new UserRepository($db);


class SinglePostController
{
    private $postRepo;
    private $userRepo;
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->postRepo = new PostRepository($db);
        $this->userRepo = new UserRepository($db);
    }


    public function getSinglePost($postId)
    {
        $singlepost = $this->postRepo->getPostById($postId);

        if (!$singlepost) {
            die("Post not found.");
        }

        return $singlepost;
    }
}



