<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 10:46
 */

namespace App\Repository\Account;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * Loads the user for the given username.
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username): ?UserInterface
    {
        return $this->createQueryBuilder('a')
            ->where('a.username = :username')
            ->andWhere('a.enabled = :true')
            ->setParameters(
                [
                    'username' => $username,
                    'true' => true,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }
}
