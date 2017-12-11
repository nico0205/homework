<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 15:48
 */

namespace App\DataFixtures\ORM;

use App\Entity\Account\User;
use App\Entity\Person\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UserFixtures
 */
class UsersFixtures extends Fixture
{
    public static $users = [
        'admin' => [
            'slug' => 'admin',
            'firstname' => 'Luke',
            'lastname' => 'Skywalker',
            'address' => 'Tatooine',
            'username' => 'root',
            'password' => 'root',
            'role' => 'ROLE_ADMIN',
        ],
        'writer' => [
            'slug' => 'writer',
            'firstname' => 'Admiral',
            'lastname' => 'Ackbar',
            'address' => 'Endor',
            'username' => 'writer',
            'password' => 'writer',
            'role' => 'ROLE_WRITER',
        ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (static::$users as $userSlug => $info) {
            $person = (new Person())
                ->setFirstname($info['firstname'])
                ->setLastname($info['lastname'])
                ->setAddress($info['address']);

            $user = (new User())
                ->setUsername($info['username'])
                ->setPlainPassword($info['password'])
                ->setPerson($person)
                ->addRole($info['role']);

            $manager->persist($user);
            $this->setReference('user_'.$userSlug, $user);
            $this->setReference('person_'.$userSlug, $person);
        }

        $manager->flush();
    }
}
