<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEntityController extends AbstractController {

  /**
   * Convert the payload to object.
   *
   * @param AbstractEntity $entity
   *  The entity object.
   * @param Request $request
   *  The request object.
   *
   * @return JsonResponse
   */
  protected function payloadToEntity(AbstractEntity $entity, Request $request) {

    if (!$new_data = $this->processPayload($request)) {
      return $this->error('The post is empty', Response::HTTP_BAD_REQUEST);
    }

    $flipped = array_flip($this->mapper);

    foreach ($new_data as $key => $value) {
      $entity->{$flipped[$key]} = $value;
    }
  }

  /**
   * Update the entity.
   *
   * @param AbstractEntity $entity
   *  The entity object.
   */
  protected function updateEntity(AbstractEntity $entity) {
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($entity);
    $entityManager->flush();
  }

  /**
   * Validating the entity.
   *
   * @param AbstractEntity $entity
   *  The entity object.
   * @param ValidatorInterface $validator
   *  The validator service.
   *
   * @return JsonResponse
   */
  protected function validate(AbstractEntity $entity, ValidatorInterface $validator) {
    $errors = $validator->validate($entity);

    $error_list = [];
    foreach ($errors as $property => $error) {
      $human_property = $this->mapper[$error->getPropertyPath()];
      $error_list[$human_property][] = $error->getMessage();
    }

    if ($error_list) {
      return $this->error(['message' => 'There are some errors in your request', 'errors' => $error_list], Response::HTTP_BAD_REQUEST);
    }
  }

  /**
   * Processing the payload.
   *
   * @return \Doctrine\Common\Collections\ArrayCollection
   *   Return the payload as an object.
   */
  protected function processPayload(Request $request) {
    $content = $request->getContent();

    if (!$decoded = json_decode($content, true)) {
      return;
    }

    return new ArrayCollection($decoded);
  }

  /**
   * Return a JSON error response.
   *
   * @param $error
   *  The error.
   * @param int $code
   *  The response code. Default to 404.
   *
   * @return JsonResponse
   */
  protected function error($error, $code = Response::HTTP_NOT_FOUND) {
    return $this->json(['error' => $error], $code);
  }

}
