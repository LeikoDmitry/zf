<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Navigation
 *
 * @ORM\Entity(repositoryClass="Application\Repository\NavigationRepository")
 * @ORM\Table(name="navigation")
 * @package Application\Entity
 */
class Navigation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @ORM\Column(name="route", type="string", length=100, nullable=false)
     */
    private $route;

    /**
     * @ORM\Column(name="parent", type="integer", options={"default": 0})
     */
    private $parent;


    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param  mixed  $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param  mixed  $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param  mixed  $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param  mixed  $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

}