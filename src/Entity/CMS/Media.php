<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 11:55
 */

namespace App\Entity\CMS;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Interfaces\UploadableInterface;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ApiResource(
 *     collectionOperations={
 *      "get"={"method"="GET"},
 *      "post"={"method"="POST", "access_control"="is_granted('ROLE_WRITER')"}
 *     },
 *     itemOperations={
 *      "get"={"method"="GET"},
 *      "delete"={"method"="DELETE", "access_control"="is_granted('ROLE_WRITER')"}
 *     },
 *     attributes={
 *      "normalization_context"={"groups"={"media"}},
 *      "denormalization_context"={"groups"={"write_media"}}
 * })
 *
 * @ORM\Entity
 * @ORM\Table(name="media", schema="cms")
 */
class Media implements UploadableInterface
{
    use TimestampableTrait;

    public const BASE_FOLDER_MEDIA = '/public/media/uploads/';

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
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="([^\s]+(\.(?i)(jpg|jpeg|png|gif|bmp))$)",
     *     message="Filename must have an name and an extension (jpg/jpeg/png/gif/bmp)"
     * )
     *
     * @Groups({"article","media","write_media","write_article"})
     */
    protected $filename;

    /**
     * @var string Base 64 encoded content image
     *
     * @Groups({"write_media","write_article"})
     */
    protected $rawContent;

    /**
     * @var string Link URL to image
     *
     * @Assert\Url()
     *
     * @Groups({"write_media","write_article"})
     */
    protected $externalLink;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @Groups({"article","media"})
     */
    protected $location;

    /**
     * @var Article[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CMS\Article", mappedBy="media")
     *
     * @Groups({"media"})
     */
    protected $articles;

    /**
     * Media constructor.
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
     * @return Media
     */
    public function setId(int $id): Media
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return self
     */
    public function setFilename(string $filename): Media
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return self
     */
    public function setLocation(string $location): Media
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Article[]|Collection
     */
    public function getArticles(): ?Collection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     *
     * @return Media
     */
    public function addArticle(Article $article): Media
    {
        $this->articles->add($article);
        $article->setMedia($this);

        return $this;
    }

    /**
     * @param Article $article
     *
     * @return Media
     */
    public function removeArticle(Article $article): Media
    {
        $this->articles->remove($article);
        $article->setMedia();

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRawContent(): ?string
    {
        return $this->rawContent;
    }

    /**
     * @param string $rawContent
     *
     * @return Media
     */
    public function setRawContent(string $rawContent): Media
    {
        $this->rawContent = $rawContent;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalLink(): string
    {
        return $this->externalLink;
    }

    /**
     * @param string $externalLink
     *
     * @return Media
     */
    public function setExternalLink(string $externalLink): Media
    {
        $this->externalLink = $externalLink;

        return $this;
    }

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->rawContent && !$this->externalLink) {
            $context->buildViolation('At least rawContent or externalLink should be set')
                ->atPath('rawContent')
                ->addViolation();
        }

        if ($this->rawContent && $this->externalLink) {
            $context->buildViolation('Either use rawContent to upload image or use external link but not both')
                ->atPath('externalLink')
                ->addViolation();
        }
    }
}
