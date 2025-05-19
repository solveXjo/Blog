<?php
// src/App/Core/SimpleCache.php

namespace App\Core;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Drivers\Files\Config as FilesConfig;

class SimpleCache
{
    private static $instance = null;
    private $cache;

    private function __construct()
    {
        $cacheDir = __DIR__ . '/../../../cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Configure cache
        $defaultConfig = new ConfigurationOption([
            'path' => $cacheDir,
        ]);

        $filesConfig = new FilesConfig([
            'path'              => $cacheDir,
            'preventCacheSlams' => true,
            'cacheSlamsTimeout' => 20,
        ]);

        // Set default config and get the cache instance
        CacheManager::setDefaultConfig($defaultConfig);
        $this->cache = CacheManager::getInstance('Files', $filesConfig);
    }

    /**
     * Get the SimpleCache instance (Singleton pattern)
     *
     * @return SimpleCache
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get a cached item or execute and cache the callback if not found
     *
     * @param string $key The cache key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Function to execute and cache if item not found
     * @return mixed The cached data
     */
    public function remember($key, $ttl, $callback)
    {
        $item = $this->cache->getItem($key);

        if (!$item->isHit()) {
            $item->set($callback())
                 ->expiresAfter($ttl);

            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * Get a cached item
     *
     * @param string $key The cache key
     * @return mixed|null The cached data or null if not found
     */
    public function get($key)
    {
        $item = $this->cache->getItem($key);
        return $item->isHit() ? $item->get() : null;
    }

    /**
     * Set a cached item
     *
     * @param string $key The cache key
     * @param mixed $value The value to cache
     * @param int $ttl Time to live in seconds
     * @return bool Whether the item was successfully saved
     */
    public function set($key, $value, $ttl = 3600)
    {
        $item = $this->cache->getItem($key);
        $item->set($value)
             ->expiresAfter($ttl);

        return $this->cache->save($item);
    }

    /**
     * Delete a cached item
     *
     * @param string $key The cache key
     * @return bool Whether the item was successfully deleted
     */
    public function delete($key)
    {
        return $this->cache->deleteItem($key);
    }

    /**
     * Clear all cached items
     *
     * @return bool Whether the cache was successfully cleared
     */
    public function clear()
    {
        return $this->cache->clear();
    }

    /**
     * Get the raw cache instance
     *
     * @return \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    public function getCache()
    {
        return $this->cache;
    }
}