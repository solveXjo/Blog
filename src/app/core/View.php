<?php

namespace App\Core;

class View
{
    private $templatePath;

    public function __construct($templatePath = 'app/views/')
    {
        $this->templatePath = trim($templatePath, '/');
    }

    public function render($view, $data = [])
    {
       $content = $this->renderData($view,$data);
       return $this->renderLayout($content);
    }
    private function renderData($view,$data){
        return "hello-world";
    }
    private function renderLayout($content){
        // $file = $this->templatePath . $template . '.php';

        // if (!file_exists($file)) {
        //     throw new \Exception("Template file $file not found.");
        // }

        // extract($data);

        // ob_start();
        // include $file;

        // return ob_get_clean();
    }
}
?>

<html>
    <head></head>
    <body>
        <header>

        </header>
        <content>
            $content
        </content>
        <footer>

        </footer>
    </body>
</html>