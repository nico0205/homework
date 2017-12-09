<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 16:20
 */

namespace App\DataFixtures\ORM;

use App\Entity\CMS\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ArticlesFixtures
 */
class ArticlesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [UsersFixtures::class];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $person = $this->getReference('person1');

        for ($i = 0; $i < 2; ++$i) {
            $article = (new Article())
                ->setTitle(sprintf('Article %d', $i))
                ->setContent('lorem ispum')
                ->setAuthor($person);

            $manager->persist($article);
        }
        $manager->flush();
    }
}
