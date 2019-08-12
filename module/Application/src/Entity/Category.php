<?php


namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Exception;

/**
 * Class Category
 * @ORM\Entity(repositoryClass="\Application\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 * @package Application\Entity
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":255}})
     *
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="self", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="self", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true, default=0)
     */
    private $parent;

    /**
     * @var string
     * @ORM\Column(name="created", type="datetime")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":255}})
     */
    private $created;

    /**
     * Category constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->created = new DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int  $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param  string  $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @param  string  $created
     */
    public function setCreated(string $created): void
    {
        $this->created = $created;
    }
}