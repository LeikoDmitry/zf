<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class NavigationRepository
 *
 * Navigation repository
 *
 * @package Application\Repository
 */
class NavigationRepository extends EntityRepository
{
    /**
     * @var mixed
     */
    private $items;

    /**
     * @var mixed
     */
    private $tree;

    /**
     * Get menu items
     */
    public function getMenuItems(): array
    {
        $this->getItemsFromDb();
        $this->generateArrayItems();
        return $this->getTree() ?? [];
    }

    /**
     * @return mixed
     */
    protected function generateArrayItems()
    {
        $tree = [];
        $items = $this->createMenuArrayIndexByIdColumn();
        foreach ($items as $item => &$value) {
            if (! $value['parent']) {
                $tree[$item] = &$value;
            } else {
                $items[$value['parent']]['pages'][] = &$value;
            }
            unset($value['parent']);
        }
        $this->setTree($tree);
        return $this;
    }

    /**
     * @return $this
     */
    public function getItemsFromDb()
    {
        $query = $this->createQueryBuilder('c')->select('c.id, c.label, c.route, c.parent')->getQuery();
        $rows = $query->getResult(Query::HYDRATE_ARRAY);
        $this->setItems($rows);
        return $this;
    }

    /**
     * @return $this|array
     */
    protected function createMenuArrayIndexByIdColumn()
    {
        if (null !== $this->getItems()) {
            $array_menu = [];
            foreach ($this->getItems() as $row) {
                $id = $row['id'];
                unset($row['id']);
                $array_menu[$id] = $row;
            }
            return $array_menu;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param  mixed  $items
     */
    public function setItems($items): void
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param  mixed  $tree
     */
    public function setTree($tree): void
    {
        $this->tree = $tree;
    }
}