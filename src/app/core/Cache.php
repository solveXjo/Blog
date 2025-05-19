<?php
namespace App\Core;

use Phpfastcache\CacheManager;
use Phpfastcache\Core\Item\ExtendedCacheItemInterface;

class Cache
{
    private $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }

}