<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController {

  public function index() {
    return new JsonResponse(['welcome' => 'hello']);
  }

}
