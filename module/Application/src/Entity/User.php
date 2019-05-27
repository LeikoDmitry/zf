<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use DateTime;

/**
 * User
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="unique_key", columns={"user_name"})})
 * @ORM\Entity(repositoryClass="Application\Repository\UserRepository")
 * @Annotation\Name("user")
 */
class User
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
     * @ORM\Column(name="user_name", type="string", length=100, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":25}})
     *
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="user_password", type="string", length=100, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":25}})
     */
    private $password;

    /**
     * @var string
     *
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Validator({"name":"identical", "options":{"token":"password"}})
     * @Annotation\Attributes({"type":"password", "class":"form-control","required":"required"})
     */
    private $confirm_password;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=100, nullable=false)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"EmailAddress"})
     */
    private $email;

    /**
     * @var string
     * @Annotation\Type("Zend\Form\Element\Csrf")
     * @Annotation\Name("csrf")
     * @Annotation\Options({"csrf_options":{"timeout":600}})
     */
    private $csrf;

    /**
     * @var DateTime
     *
     * @Annotation\Filter({"name":"StringTrim"})
     * @ORM\Column(name="login_date", type="datetime", nullable=true)
     */
    private $login_date;

    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->login_date = new DateTime();
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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param  string  $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param  string  $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->confirm_password;
    }

    /**
     * @param  string  $confirm_password
     */
    public function setConfirmPassword(string $confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param  string  $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return DateTime
     */
    public function getLoginDate(): DateTime
    {
        return $this->login_date;
    }

    /**
     * @param  DateTime  $login_date
     */
    public function setLoginDate(DateTime $login_date): void
    {
        $this->login_date = $login_date;
    }

    /**
     * @return string
     */
    public function getCsrf(): string
    {
        return $this->csrf;
    }

    /**
     * @param  string  $csrf
     */
    public function setCsrf(string $csrf): void
    {
        $this->csrf = $csrf;
    }
}