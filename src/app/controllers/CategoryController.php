<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\PostRepository;

use App\Core\Route;
use PDO;

class CategoryController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->postRepo = new PostRepository($this->db);
    }

    public function show()
    {
        $currentCategory = $this->getCurrentCategory();
        $postsData = $this->getPaginatedPosts($currentCategory);

        $currentCategory = $this->getCurrentCategory();
        $categories = $this->postRepo->getAllCategories();
        $pageTitle = $this->getPageTitle($currentCategory);

        echo $this->view->renderWithLayout('category.view.php', 'layouts/main.php', [
            'title' => "Category",
            'posts' => $postsData['posts'],
            'totalPages' => $postsData['totalPages'],
            'currentPage' => $postsData['currentPage'],
            'currentCategory' => $currentCategory
        ]);
    }
    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /home');
            exit();
        }

        $this->show();
    }

    public function getImagePath()
    {
        return $_GET['image_path'] ?? "";
    }

    public function getAllCategories()
    {
        return $this->postRepo->getAllCategories();
    }

    public function getPostsByCategory($currentCategory)
    {
        return !empty($currentCategory)
            ? $this->postRepo->getPostsByCategory($currentCategory)
            : $this->postRepo->getAllPosts();
    }

    public function getPageTitle($currentCategory)
    {
        return !empty($currentCategory) ? $currentCategory . " Posts" : "All Posts";
    }

    public function mergeQuery(array $newParams = [])
    {
        $currentParams = $_GET;
        $mergedParams = array_merge($currentParams, $newParams);

        $baseUri = "/category";
        if (!empty($currentParams['category'])) {
            $baseUri .= "/" . $currentParams['category'];
        }

        $queryString = http_build_query(array_filter($mergedParams, function ($key) {
            return $key !== 'category';
        }, ARRAY_FILTER_USE_KEY));

        return $queryString ? $baseUri . "?" . $queryString : $baseUri;
    }

    public function getPaginatedPosts($currentCategory, $perpage = 4)
    {

        $query = "SELECT COUNT(*) as total FROM posts";
        if (!empty($currentCategory)) {
            $query .= " WHERE category = :category";
        }

        $stmt = $this->db->connection->prepare($query);
        if (!empty($currentCategory)) {
            $stmt->bindParam(':category', $currentCategory, PDO::PARAM_STR);
        }
        $stmt->execute();
        $totalPosts = $stmt->fetchColumn();
        $totalPages = ceil($totalPosts / $perpage);

        $currentPage = $_GET['page'] ?? 1;
        $currentPage = max(1, min($currentPage, $totalPages)); // Ensure page is within bounds
        $offset = ($currentPage - 1) * $perpage;

        $query = "SELECT * FROM posts";
        if (!empty($currentCategory)) {
            $query .= " WHERE category = :category";
        }
        $query .= " ORDER BY id DESC LIMIT :offset, :perpage";

        $stmt = $this->db->connection->prepare($query);
        if (!empty($currentCategory)) {
            $stmt->bindParam(':category', $currentCategory, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perpage', $perpage, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'posts' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ];
    }
}
