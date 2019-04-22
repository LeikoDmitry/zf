<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class CacheFactory
 *
 * @package Application\Service
 */
class CacheFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface  $container
     * @param  string  $requestedName
     * @param  array|null  $options
     *
     * @return $this|object|\Zend\Cache\Storage\StorageInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Cache($container->get('config'));
    }
}