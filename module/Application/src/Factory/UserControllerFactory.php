<?php

namespace Application\Factory;


use Application\Controller\UserController;
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
        return new UserController($container->get(AuthenticationService::class), new SessionManager());
    }
}