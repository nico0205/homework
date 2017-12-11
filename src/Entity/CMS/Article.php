<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 09:21
 */

namespace App\Entity\CMS;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Person\Person;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST", "access_control"="is_granted('ROLE_WRITER')"}
 *     },
 *     itemOperations={
 *      "get"={"method"="GET"},
 *      "put"={"method"="PUT", "access_control"="is_granted('ROLE_WRITER')"},
 *      "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_WRITER')"}
 *     },
 *     attributes={
 *      "normalization_context"={"groups"={"article"}},
 *      "denormalization_context"={"groups"={"write_article"}}
 * })
 *
 * @ORM\Entity(repositoryClass="App\Repository\CMS\ArticleRepository")
 * @ORM\Table(name="article", schema="cms")
 */
class Article
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
     * @Groups({"article","person","write_article"})
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"id","title"})
     *
     * @Groups("article")
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @Groups({"article","write_article"})
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $content;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Person\Person", inversedBy="articles")
     *
     * @Groups("article")
     */
    protected $author;

    /**
     *
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CMS\Media", inversedBy="articles", cascade={"PERSIST"})
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Valid()
     *
     * @Groups({"article","write_article"})
     */
    protected $media;

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
     * @return Article
     */
    public function setId(int $id): Article
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Article
     */
    public function setTitle(string $title): Article
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Article
     */
    public function setSlug(string $slug): Article
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Article
     */
    public function setContent(string $content): Article
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Person|null
     */
    public function getAuthor(): ?Person
    {
        return $this->author;
    }

    /**
     * @param Person $author
     *
     * @return Article
     */
    public function setAuthor(?Person $author = null): Article
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Media|null
     */
    public function getMedia(): ?Media
    {
        return $this->media;
    }

    /**
     * @param Media|null $media
     *
     * @return Article
     */
    public function setMedia(?Media $media = null): Article
    {
        $this->media = $media;

        return $this;
    }
}
