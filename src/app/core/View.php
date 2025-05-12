<?php

namespace App\Core;

class View
{
    protected $path;
    protected $data = [];

    public function __construct(string $path = 'src/app/views')
    {
        $this->path = rtrim($path, '/') . '/';
    }

    public function render(string $template, array $data = []): string
    {
        extract(array_merge($this->data, $data));

        ob_start();
        include $this->path . $template;
        $content = ob_get_clean();

        return $content;
    }

    public function renderWithLayout(string $template, string $layout = 'layouts/main.php', array $data = []): string
    {
        $data = array_merge([
            'title' => 'Default Title',
            'page Title' => '',
            'content' => ''
        ], $data);

        // First render the template to get content
        $data['content'] = $this->render($template, $data);

        // Then render the layout with all variables
        return $this->render($layout, $data);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}
