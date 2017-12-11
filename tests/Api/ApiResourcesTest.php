<?php declare(strict_types=1);

/**
 * PHP version 7
 *
 * Created by PhpStorm.
 * User: nicolas
 * Date: 11/12/17
 * Time: 15:07
 *
 * @category   homework
 *
 * @package    ${NAMESPACE}
 *
 * @subpackage ${NAMESPACE}
 *
 * @author     Nicolas Demay <nicolas.demay@actiane.com>
 */

namespace App\Tests\Api;

use App\DataFixtures\ORM\ArticlesFixtures;
use App\DataFixtures\ORM\UsersFixtures;
use App\Entity\Account\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ApiResourcesTest
 */
class ApiResourcesTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * @return array
     */
    public function getFixtures(): array
    {
        return [
            ArticlesFixtures::class,
            UsersFixtures::class,
        ];
    }

    /**
     * @small
     *
     * @dataProvider routeProvider
     *
     * @param string      $routeName
     * @param string      $method
     * @param array       $routeParam
     * @param string|null $userSlug
     * @param int         $expectedCode
     * @param string|null $body
     *
     * @throws \Doctrine\Common\DataFixtures\OutOfBoundsException
     */
    public function testAllRoute(string $routeName, string $method, array $routeParam, ?string $userSlug, int $expectedCode, ?string $body): void
    {
        $url = $this->getContainer()->get('router')->generate($routeName, $routeParam);
        if ($userSlug) {
            /** @var User $user */
            $user = $this->fixtures->getReference($userSlug);
            $this->loginAs($user, 'main');
        }
        $client = $this->makeClient();

        $headers['CONTENT_TYPE'] = 'application/json';
        $headers['Accept'] = 'application/json';

        $client->request(
            $method,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $body
        );
        $this->assertStatusCode($expectedCode, $client);
    }

//    /**
//     * @small
//     *
//     * @dataProvider userProvider
//     *
//     * @param string $slugUser
//     *
//     * @throws \Doctrine\Common\DataFixtures\OutOfBoundsException
//     */
//    public function testLoginSuccess(string $slugUser)
//    {
//        // dunno why this doesn\'t work
//
//        /** @var User $user */
//        $user = $this->fixtures->getReference('user_'.$slugUser);
//        $url = $this->getContainer()->get('router')->generate('login_check');
//        $client = $this->makeClient();
//        $client->request(
//            'POST',
//            $url,
//            [
//                'username' => $user->getUsername(),
//                'password' =>
//                    $user->getPassword(),
//            ]
//        );
//        $this->assertStatusCode(200, $client);
//    }

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->fixtures = $this->loadFixtures($this->getFixtures())->getReferenceRepository();
    }

    /**
     * @return array
     */
    public function userProvider(): array
    {
        return UsersFixtures::$users;
    }

    /**
     * @return array
     */
    public function routeProvider(): array
    {
        $userPostSample = json_encode(
            [
                'username' => 'string',
                'plainPassword' => 'string',
                'person' => [
                    'lastname' => 'string',
                    'firstname' => 'string',
                    'middlename' => 'string',
                    'address' => 'string',
                ],
            ]
        );

        $mediaPostSample = json_encode(
            [
                'filename' => 'string',
                'rawContent' => 'string',
                'externalLink' => 'string',
            ]
        );

        $articlePostSample = json_encode(
            [
                'title' => 'string',
                'content' => 'string',
                'media' => [
                    'filename' => 'string',
                    'rawContent' => 'string',
                    'externalLink' => 'string',
                ],
            ]
        );

        $peoplePostSample = json_encode(
            [
                'lastname' => 'string',
                'firstname' => 'string',
                'middlename' => 'string',
                'address' => ';string',
            ]
        );

        // string route name
        // string method
        // array route param
        // string userSlug (getting from reference)
        // int status code expected
        // array param in body

        return [
            'api_entrypoint' => ['api_entrypoint', 'GET', [], null, 200, null],
            'api_doc' => ['api_doc', 'GET', [], null, 200, null],
            'api_jsonld_context' => ['api_jsonld_context', 'GET', ['shortName' => 'Article'], null, 200, null],

            // route protected for admin only
            'api_users_get_collection_forbidden' => ['api_users_get_collection', 'GET', [], null, 401, null],
            'api_users_get_collection_granted' => ['api_users_get_collection', 'GET', [], 'user_admin', 200, null],
            'api_users_post_collection' => [
                'api_users_post_collection',
                'POST',
                [],
                'user_admin',
                200,
                $userPostSample,
            ],
            'api_users_get_item_forbidden' => ['api_users_get_item', 'GET', ['id' => 1], null, 401, null],
            'api_users_get_item_granted' => ['api_users_get_item', 'GET', ['id' => 1], 'user_admin', 200, null],
            'api_users_put_item_forbidden' => ['api_users_put_item', 'PUT', ['id' => 1], null, 401, null],
            'api_users_put_item_granted' => ['api_users_put_item', 'PUT', ['id' => 1], 'user_admin', 200, null],
            'api_users_delete_item_forbidden' => ['api_users_delete_item', 'DELETE', ['id' => 1], null, 401, null],
            'api_users_delete_item_granted' => [
                'api_users_delete_item',
                'DELETE',
                ['id' => 1],
                'user_admin',
                200,
                null,
            ],

            // allowed for anonymous
            'api_media_get_collection' => ['api_media_get_collection', 'GET', [], null, 200, null],

            // allow for writer
            'api_media_post_collection_forbidden' => [
                'api_media_post_collection',
                'POST',
                [],
                null,
                401,
                $mediaPostSample,
            ],
            'api_media_post_collection' => [
                'api_media_post_collection',
                'POST',
                [],
                'user_writer',
                200,
                $mediaPostSample,
            ],

            // allow for anonymous
            'api_media_get_item' => ['api_media_get_item', 'GET', ['id' => 1], null, 200, null],

            // allow for writer
            'api_media_delete_item_forbidden' => ['api_media_delete_item', 'DELETE', ['id' => 1], null, 401, null],
            'api_media_delete_item_granted' => [
                'api_media_delete_item',
                'DELETE',
                ['id' => 1],
                'user_writer',
                200,
                null,
            ],

            // allow for anonymous
            'api_articles_get_collection' => ['api_articles_get_collection', 'GET', [], null, 200, null],

            // allow for writer
            'api_articles_post_collection_forbidden' => [
                'api_articles_post_collection',
                'POST',
                [],
                null,
                401,
                $articlePostSample,
            ],
            'api_articles_post_collection_granted' => [
                'api_articles_post_collection',
                'POST',
                [],
                'user_writer',
                200,
                $articlePostSample,
            ],

            // allow for anonymous
            'api_articles_get_item' => ['api_articles_get_item', 'GET', ['id' => 1], null, 200, null],

            // allow for writer
            'api_articles_put_item_forbidden' => [
                'api_articles_put_item',
                'PUT',
                ['id' => 1],
                null,
                401,
                $articlePostSample,
            ],
            'api_articles_put_item_granted' => [
                'api_articles_put_item',
                'PUT',
                ['id' => 1],
                'user_writer',
                200,
                $articlePostSample,
            ],

            'api_articles_delete_forbidden' => ['api_articles_delete_item', 'DELETE', ['id' => 1], null, 401, null],
            'api_articles_delete_granted' => [
                'api_articles_delete_item',
                'DELETE',
                ['id' => 1],
                'user_writer',
                200,
                null,
            ],

            // allow for anonymous
            'api_people_get_collection' => ['api_people_get_collection', 'GET', [], null, 200, null],

            // allow for admin
            'api_people_post_collection_forbidden' => [
                'api_people_post_collection',
                'POST',
                [],
                null,
                401,
                $peoplePostSample,
            ],
            'api_people_post_collection_granted' => [
                'api_people_post_collection',
                'POST',
                [],
                'user_admin',
                200,
                $peoplePostSample,
            ],

            'api_people_put_item_forbidden' => [
                'api_people_put_item',
                'PUT',
                ['id' => 1],
                null,
                401,
                $peoplePostSample,
            ],
            'api_people_put_item_granted' => [
                'api_people_put_item',
                'PUT',
                ['id' => 1],
                'user_admin',
                200,
                $peoplePostSample,
            ],

            'api_people_delete_item_forbidden' => ['api_people_delete_item', 'DELETE', ['id' => 1], null, 401, null],
            'api_people_delete_item_granted' => [
                'api_people_delete_item',
                'DELETE',
                ['id' => 1],
                'user_admin',
                200,
                null,
            ],

            // allow for anonymous
            'api_people_get_item' => ['api_people_get_item', 'GET', ['id' => 1], null, 200, null],
        ];
    }
}