<?php

namespace Application;

/**
 * Class Module
 *
 * @package Application
 */
class Module
{
    const VERSION = '3.0.3-dev';

    /**
     * Return module config
     *
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
