<!--still not finished -->
<?php
class PostsView
{
    private $posts;
    private $layout = 'default.php';

    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    public function render()
    {
        ob_start();
        extract(['posts' => $this->posts]);
        include __DIR__ . '/../resources/views/posts.view.php';
        $content = ob_get_clean();
        include __DIR__ . '/../resources/layouts/' . $this->layout;
    }
}
