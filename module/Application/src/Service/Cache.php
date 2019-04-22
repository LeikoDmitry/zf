<?php

namespace Application\Service;

use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

/**
 * Class Cache
 *
 * @package Application\Service
 */
class Cache
{
    /**
     * @var string
     */
    const NAV_CACHE = 'navigation_cache';

    /**
     * @var array
     */
    private $config = [];

    /**
     * Cache constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return StorageInterface
     */
    public function getCacheInstance()
    {
        return StorageFactory::factory($this->config['cache']);
    }
}