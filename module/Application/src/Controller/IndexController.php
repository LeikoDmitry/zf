<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 *
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * Create
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }
}
