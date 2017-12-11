<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/12/17
 * Time: 12:11
 *
 * @category   homework
 *
 * @package    App\Tests
 *
 * @subpackage App\Tests
 *
 * @author     Nicolas Demay <nicolas.demay@actiane.com>
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractTestCase
 */
abstract class AbstractTestCase extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * setUp is lauched on every test class
     */
    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }
}
