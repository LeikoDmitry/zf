<?php

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class BlogController
 *
 * Blog controller application
 * @package Application\Controller
 */
class BlogController extends AbstractActionController
{
    /**
     * Landing page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Detail posts
     *
     * @return ViewModel
     */
    public function detailAction()
    {
        return new ViewModel();
    }
}