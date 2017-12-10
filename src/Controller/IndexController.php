<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 14:31
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 */
class IndexController extends AbstractController
{
    /**
     * @Route(path="/", name="index")
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function index(): Response
    {
        return $this->redirectToRoute('api_entrypoint');
    }
}
