<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 11:05
 */

namespace App\Entity\Person;

use App\Entity\Account\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Person\PersonRepository")
 * @ORM\Table(name="person", schema="person")
 */
class Person
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $middlename;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $address;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Account\User", mappedBy="person")
     */
    protected $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Person
     */
    public function setId(int $id): Person
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Person
     */
    public function setName(string $name): Person
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     *
     * @return Person
     */
    public function setFirstname(string $firstname): Person
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    /**
     * @param string|null $middlename
     *
     * @return Person
     */
    public function setMiddlename(?string $middlename): Person
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return Person
     */
    public function setAddress(?string $address): Person
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return Person
     */
    public function setUser(User $user): Person
    {
        $user->setPerson($this);

        return $this;
    }
}
