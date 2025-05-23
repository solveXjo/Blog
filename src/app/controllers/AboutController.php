<?php

namespace App\Controllers;

use App\Core\View;

use App\Core\Route;

class AboutController
{

    private $view;

    private $postData;

    public function __construct()
    {
        $this->view = new View();
        $this->view->title = 'About - Altibbi';

    }
    #[Route('/about', 'GET')]

    public function show()
    {
        echo $this->view->renderWithLayout('about.view.php', 'layouts/main.php', [
            'title' => 'about - Altibbi',
            'postData' => $this->postData
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
}
