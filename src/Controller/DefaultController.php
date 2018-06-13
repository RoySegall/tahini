<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller with the basic information about the system.
 */
class DefaultController extends AbstractController {

    public const VERSION = '2.1';

    /**
     * @Route("/", methods={"GET"})
     *
     * @param Request $request
     *  The request object.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *  Return a json info about the system.
     */
    public function index(Request $request) {
        return $this->json([
            'title' => 'Taliaz Health',
            'Sys Admin' => [
                'name'  => 'Sagee Lupin',
                'email' => 'tech.team@taliazhealth.com',
            ],
            'version' => self::VERSION,
            'env' => getenv('APP_ENV'),
            'ip_address' => $request->getClientIp(),
        ]);
    }

}
