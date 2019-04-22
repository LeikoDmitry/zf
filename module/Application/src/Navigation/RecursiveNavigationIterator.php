<?php

namespace Application\Navigation;

use RecursiveIteratorIterator;

/**
 * Class RecursiveNavigationIterator
 *
 * @package Application\Navigation
 */
class RecursiveNavigationIterator extends RecursiveIteratorIterator
{
    /**
     * Start iteration menu
     */
    public function beginIteration()
    {
        print('<ul class="nav navbar-nav">') . PHP_EOL;
    }

    /**
     * End iteration menu
     */
    public function endIteration()
    {
        print('</ul>') . PHP_EOL;
    }

    /**
     * Start children if exist
     */
    public function beginChildren()
    {
        print('<ul class="dropdown-menu">') . PHP_EOL;
    }

    /**
     * End children
     */
    public function endChildren()
    {
        print('</ul></li>') . PHP_EOL;
    }
}