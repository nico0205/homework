<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 11:05
 */

namespace App\Entity\Person;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Account\User;
use App\Entity\CMS\Article;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"person"}},
 *     "denormalization_context"={"groups"={"write_person"}}
 * })
 * })
 *
 * @ORM\Entity(repositoryClass="App\Repository\Person\PersonRepository")
 * @ORM\Table(name="person", schema="person")
 */
class Person
{
    use TimestampableTrait;

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
     *
     * @Groups({"person","write_person","write_user"})
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @Groups({"person","write_person","write_user"})
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"person","write_person","write_user"})
     */
    protected $middlename;

    /**
     * @var string
     *
     * @Groups({"article","user"})
     */
    protected $fullname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"person","write_person","write_user"})
     */
    protected $address;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Account\User", mappedBy="person")
     *
     * @Groups("person")
     */
    protected $user;

    /**
     * @var Article[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CMS\Article", mappedBy="author")
     *
     * @Groups("person")
     */
    protected $articles;

    /**
     * Person constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

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
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return Person
     */
    public function setLastname(string $lastname): Person
    {
        $this->lastname = $lastname;

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
     * @return string
     */
    public function getFullname(): string
    {
        if ($this->middlename) {
            return sprintf('%s %s %s', $this->firstname, $this->middlename, $this->lastname);
        }

        return sprintf('%s %s', $this->firstname, $this->lastname);
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
     * @param User $user
     *
     * @return Person
     */
    public function setUser(User $user): Person
    {
        $user->setPerson($this);

        return $this;
    }

    /**
     * @return Article[]|Collection
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     *
     * @return Person
     */
    public function addArticles(Article $article): Person
    {
        $this->articles->add($article);
        $article->setAuthor($this);

        return $this;
    }

    /**
     * @param Article $article
     *
     * @return Person
     */
    public function removeArticle(Article $article): Person
    {
        $this->articles->remove($article);
        $article->setAuthor();

        return $this;
    }
}
