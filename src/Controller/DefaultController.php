<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
class DefaultController extends AbstractController
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return new Response('Up & Running');
    }
}
