<?php


namespace Application\Service;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SmtpFactory
 *
 * @package Application\Service
 */
class SmtpFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface  $container
     * @param  string  $requestedName
     * @param  array|null  $options
     *
     * @return Smtp|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Smtp($container->get('config'));
    }
}