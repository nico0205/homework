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
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $person = (new Person())
            ->setFirstname('Luke')
            ->setLastname('Skywalker')
            ->setAddress('Tatooine');

        $user = (new User())
            ->setUsername('root')
            ->setPlainPassword('root')
            ->setPerson($person);

        $manager->persist($user);
        $this->setReference('user1', $user);
        $this->setReference('person1', $person);
        $manager->flush();
    }
}
