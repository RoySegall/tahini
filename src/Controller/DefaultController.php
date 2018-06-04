<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController {

  public function index() {
    return new JsonResponse(['welcome' => 'hello']);
  }

}
