<?php


namespace App\Core;

class Cache
{
    private $path;
    private $ttl;

    public function __construct(array $config)
    {
        $this->path = $config['path'] ??   'storage/cache';
        $this->ttl = $config['ttl'] ?? 3600;

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function get(string $key)
    {
        $file = $this->path . '/' . md5($key);

        if (file_exists($file) && (time() - filemtime($file) < $this->ttl)) {
            return unserialize(file_get_contents($file));
        }

        return null;
    }

    public function set(string $key, $value): bool
    {
        $file = $this->path . '/' . md5($key);
        return file_put_contents($file, serialize($value)) !== false;
    }

    public function delete(string $key): bool
    {
        $file = $this->path . '/' . md5($key);
        return file_exists($file) ? unlink($file) : false;
    }
}
