<?php

namespace Application\Factory;


use Application\Controller\UserController;
use Application\Service\Smtp;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

/**
 * Class UserControllerFactory
 *
 * @package Application\Factory
 */
class UserControllerFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface  $container
     * @param  string  $requestedName
     * @param  array|null  $options
     *
     * @return UserController|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserController(
            $container->get(AuthenticationService::class),
            $container->get(SessionManager::class),
            $container->get('ContainerAuthentication'),
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(Smtp::class),
            $container->get('ViewRenderer')
        );
    }
}