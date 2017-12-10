<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 17:15
 */

namespace App\EventSubscriber\Doctrine;

use App\Entity\CMS\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ArticleSetAuthorListener
 */
class ArticleSetAuthorListener implements EventSubscriber
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * ArticleSetAuthorListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return ['prePersist'];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Article) {
            return;
        }

        if (!$token = $this->tokenStorage->getToken()) {
            // if we got no token
            // should be impossible as the route is protected
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // to prevent dump error
            return;
        }
        $entity->setAuthor($user->getPerson());
    }
}
