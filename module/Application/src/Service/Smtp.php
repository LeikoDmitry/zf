<?php

namespace Application\Service;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Class SmtpService
 *
 * @package Application\Service
 */
class Smtp
{
    /**
     * @var SmtpTransport
     */
    private $smtpTransport;

    /**
     * SmtpService constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->smtpTransport = new SmtpTransport();
        $options = new SmtpOptions($config['smtp_options']);
        $this->smtpTransport->setOptions($options);
    }

    /**
     * @return $this|SmtpTransport
     */
    public function getSmtpTransport()
    {
        if (null !== $this->smtpTransport) {
            return $this->smtpTransport;
        }
        return $this;
    }
}