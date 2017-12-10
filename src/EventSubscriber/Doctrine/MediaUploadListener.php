<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 12:21
 */

namespace App\EventSubscriber\Doctrine;

use App\Interfaces\UploadableInterface;
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
     * MediaUploadListener constructor.
     *
     * @param Filesystem $filesystem
     * @param string     $kernelRootDir
     */
    public function __construct(Filesystem $filesystem, string $kernelRootDir)
    {
        $this->filesystem = $filesystem;
        $this->kernelRootDir = $kernelRootDir;
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
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UploadableInterface) {
            return;
        }

        $this->createFileFromRaw($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UploadableInterface) {
            return;
        }

        $this->createFileFromRaw($entity);

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(\get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * @param UploadableInterface $uploadable
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    private function createFileFromRaw(UploadableInterface $uploadable): void
    {
        $mediaLocation = $this->kernelRootDir.'/public/media/uploads/';
        if (!$this->filesystem->exists($mediaLocation)) {
            $this->filesystem->mkdir($mediaLocation);
        }

        [$name, $extension] = explode('.', $uploadable->getFileName());

        $path = sprintf('%s%s%s.%s', $mediaLocation, $name, uniqid('_img'), $extension);

        $this->filesystem->touch($path);

        // raw data is expected to be data:image/png;base64,AAAABBBCCCDDEEF
        // we first extract encoding then data
        // we could eventually inspect file type there and throw exception against some allowed types
        $data = explode(';', $uploadable->getRawContent())[1];
        // then we extract only raw data
        $data = explode(',', $data)[1];

        $this->filesystem->dumpFile($path, base64_decode($data));
        $relPath = $this->filesystem->makePathRelative($path, $this->kernelRootDir.'/public/');

        $uploadable->setFileName($uploadable->getFileName());
        $uploadable->setLocation('./'.rtrim($relPath, '/'));
    }
}
