<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/12/17
 * Time: 11:01
 *
 * @category   homework
 *
 * @package    App\Tests\Service
 *
 * @subpackage App\Tests\Service
 *
 * @author     Nicolas Demay <nicolas.demay@actiane.com>
 */

namespace App\Tests\Service;

use App\Exceptions\NotAnImageException;
use App\Exceptions\NotAnUrlException;
use App\Service\CurlBase64Image;
use App\Tests\AbstractTestCase;

/**
 * Class CurlBase64ImageTest
 */
class CurlBase64ImageTest extends AbstractTestCase
{
    /**
     * @small
     *
     * @throws \App\Exceptions\CurlBase64ImageException
     * @throws \App\Exceptions\NotAnUrlException
     * @throws \App\Exceptions\NotAnImageException
     */
    public function testGetBase64ImageSuccess(): void
    {
        $url = 'http://lorempicsum.com/simpsons/350/200/1';
        /** @var CurlBase64Image $manager */
        $manager = $this->getContainer()->get(CurlBase64Image::class);
        $this->assertNotNull($manager->getBase64EncodedImage($url));
    }

    /**
     * @small
     *
     * @throws \App\Exceptions\CurlBase64ImageException
     * @throws \App\Exceptions\NotAnUrlException
     * @throws \App\Exceptions\NotAnImageException
     */
    public function testGetBase64ImageFailWithNotAnUrl(): void
    {
        $this->expectException(NotAnUrlException::class);
        $url = 'not an url';
        /** @var CurlBase64Image $manager */
        $manager = $this->getContainer()->get(CurlBase64Image::class);
        $manager->getBase64EncodedImage($url);
    }

    /**
     * @small
     *
     * @throws \App\Exceptions\CurlBase64ImageException
     * @throws \App\Exceptions\NotAnUrlException
     * @throws \App\Exceptions\NotAnImageException
     */
    public function testGetBase64ImageFailWithNotAnImage(): void
    {
        $this->expectException(NotAnImageException::class);
        $url = 'http://www.google.fr';
        /** @var CurlBase64Image $manager */
        $manager = $this->getContainer()->get(CurlBase64Image::class);
        $manager->getBase64EncodedImage($url);
    }
}
