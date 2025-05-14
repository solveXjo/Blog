<?php

namespace App\Controllers;

use App\Core\BaseController;

class ErrorController extends BaseController
{
    public function notFound()
    {
        echo $this->view->renderWithLayout('/errors/404.view.php', 'layouts/main.php', [
            'title' => 'Error - Altibbi',
            'message' => 'The requested page could not be found.',
        ]);
    }

    public function error()
    {
        $message = $_GET['message'] ?? 'An unknown error occurred.';
        echo $this->view->renderWithLayout('/errors/404.view.php', 'layouts/main.php', [
            'title' => 'Error - Altibbi',
            'message' => $message,
        ]);
    }
}
