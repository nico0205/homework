<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 16:20
 */

namespace App\DataFixtures\ORM;

use App\Entity\CMS\Article;
use App\Entity\CMS\Media;
use App\Service\CurlBase64Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ArticlesFixtures
 */
class ArticlesFixtures extends Fixture implements DependentFixtureInterface
{
    public const BASE_IMAGE_EXEMPLE = 'http://lorempicsum.com/simpsons/350/200/';

    /**
     * @var CurlBase64Image
     */
    private $image;

    /**
     * ArticlesFixtures constructor.
     *
     * @param CurlBase64Image $image
     */
    public function __construct(CurlBase64Image $image)
    {
        $this->image = $image;
    }

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
     *
     * @throws \App\Exceptions\CurlBase64ImageException
     */
    public function load(ObjectManager $manager)
    {
        $person = $this->getReference('person_writer');

        for ($i = 1; $i < 3; ++$i) {
            $media = (new Media())
                ->setFilename('article'.$i.'.jpg')
                ->setRawContent($this->image->getBase64EncodedImage(self::BASE_IMAGE_EXEMPLE.$i));

            $article = (new Article())
                ->setTitle(sprintf('Article %d', $i))
                ->setContent('lorem ispum')
                ->setAuthor($person)
                ->setMedia($media);

            $manager->persist($article);
        }
        $manager->flush();
    }
}
