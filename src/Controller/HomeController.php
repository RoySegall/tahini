<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller with the basic information about the system.
 */
class HomeController extends AbstractController
{

    public const VERSION = '2.1';

  /**
   * @Route("/", methods={"GET"})
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *  Return a json info about the system.
   */
    public function index()
    {
        return $this->json(['message' => 'Welcome!']);
    }
}
