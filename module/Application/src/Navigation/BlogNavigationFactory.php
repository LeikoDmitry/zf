<?php

namespace Application\Navigation;


use Application\Entity\Navigation;
use Application\Repository\NavigationRepository;
use Application\Service\Cache;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\Navigation\Service\AbstractNavigationFactory;


/**
 * Class BlogNavigationFactory
 *
 * @package Application\Navigation
 */
class BlogNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @var string
     */
    const NAVIGATION_NAME = 'application-nav';

    /**
     * Name menu
     *
     * @return string
     */
    public function getName()
    {
        return static::NAVIGATION_NAME;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return array|null
     */
    public function getPages(ContainerInterface $container)
    {
        $entity_manager = $container->get(EntityManager::class);
        /** @var  $navigation_repository NavigationRepository */
        $navigation_repository = $entity_manager->getRepository(Navigation::class);
        /** @var  $cache Cache */
        $cache = $container->get(Cache::class);
        $instance = $cache->getCacheInstance();
        if (! $instance->hasItem(Cache::NAV_CACHE)) {
            $instance->setItem(Cache::NAV_CACHE, $navigation_repository->getMenuItems());
            return $this->preparePages($container, $instance->getItem(Cache::NAV_CACHE));
        }
        $pages = $instance->getItem(Cache::NAV_CACHE);
        return $this->preparePages($container, $pages);
    }
}