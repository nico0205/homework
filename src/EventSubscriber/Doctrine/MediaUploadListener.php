<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 12:21
 */

namespace App\EventSubscriber\Doctrine;

use App\Entity\CMS\Media;
use App\Interfaces\UploadableInterface;
use App\Service\CurlBase64Image;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class MediaUploadListener
 */
class MediaUploadListener implements EventSubscriber
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var CurlBase64Image
     */
    private $base64Image;

    /**
     * MediaUploadListener constructor.
     *
     * @param Filesystem      $filesystem
     * @param CurlBase64Image $base64Image
     * @param string          $kernelRootDir
     */
    public function __construct(Filesystem $filesystem, CurlBase64Image $base64Image, string $kernelRootDir)
    {
        $this->filesystem = $filesystem;
        $this->kernelRootDir = $kernelRootDir;
        $this->base64Image = $base64Image;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return ['prePersist', 'preUpdate'];
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \App\Exceptions\NotAnUrlException
     * @throws \App\Exceptions\NotAnImageException
     * @throws \App\Exceptions\CurlBase64ImageException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UploadableInterface) {
            return;
        }

        $this->processUpload($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \App\Exceptions\NotAnUrlException
     * @throws \App\Exceptions\NotAnImageException
     * @throws \App\Exceptions\CurlBase64ImageException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UploadableInterface) {
            return;
        }

        $this->processUpload($entity);

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(\get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * @param UploadableInterface $uploadable
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \App\Exceptions\CurlBase64ImageException
     * @throws \App\Exceptions\NotAnImageException
     * @throws \App\Exceptions\NotAnUrlException
     */
    private function processUpload(UploadableInterface $uploadable): void
    {
        if ($uploadable->getRawContent()) {
            $this->createFileFromRaw($uploadable);

            // if we get raw content we don't create file from URL even if it's provided

            return;
        }

        if ($uploadable->getExternalLink()) {
            $uploadable->setRawContent($this->base64Image->getBase64EncodedImage($uploadable->getExternalLink()));
        }

        $this->createFileFromRaw($uploadable);
    }

    /**
     * @param UploadableInterface $uploadable
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    private function createFileFromRaw(UploadableInterface $uploadable): void
    {
        $mediaLocation = $this->kernelRootDir.Media::BASE_FOLDER_MEDIA;
        if (!$this->filesystem->exists($mediaLocation)) {
            $this->filesystem->mkdir($mediaLocation);
        }

        [$name, $extension] = explode('.', $uploadable->getFileName());

        $path = sprintf('%s%s%s.%s', $mediaLocation, $name, uniqid('_img'), $extension);

        $this->filesystem->touch($path);

        $this->filesystem->dumpFile($path, base64_decode($uploadable->getRawContent()));
        $relPath = $this->filesystem->makePathRelative($path, $this->kernelRootDir.'/public/');

        $uploadable->setFileName($uploadable->getFileName());
        $uploadable->setLocation('./'.rtrim($relPath, '/'));
    }
}
