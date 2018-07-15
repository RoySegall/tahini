<?php

namespace App\Services;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\AbstractEntity;

/**
 * Taliaz API validator service.
 *
 * Get the entity constraints.
 *
 * @package App\Services
 */
class TaliazValidator {

  /**
   * @var ValidatorInterface
   */
  protected $validator;

    /**
     * TaliazValidator constructor.
     *
     * @param ValidatorInterface $validator
     *  The validator service.
     */
  public function __construct(ValidatorInterface $validator) {
    $this->validator = $validator;
  }

  /**
   * @param AbstractEntity $entity
   *
   * @return array
   */
  public function validate(AbstractEntity $entity) : array {

    $error_list = [];

    try {
      $errors = $this->validator->validate($entity);

      if (!$errors) {
        return [];
      }
    } catch (ValidationException $validationException) {
      foreach ($validationException->getConstraintViolationList() as $error) {
        $human_property = $entity->getMapper()[$error->getPropertyPath()];
        $error_list[$human_property][] = $error->getMessage();
      }
    }

    return $error_list;
  }

}
