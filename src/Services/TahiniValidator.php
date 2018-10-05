<?php

namespace App\Services;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\AbstractEntity;

/**
 * Tahini API validator service.
 *
 * Get the entity constraints.
 *
 * @package App\Services
 */
class TahiniValidator
{

  /**
   * @var ValidatorInterface
   */
    protected $validator;

    /**
     * TahiniValidator constructor.
     *
     * @param ValidatorInterface $validator
     *  The validator service.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

  /**
   * Validating the entity object by using the constraints service.
   *
   * @param AbstractEntity $entity
   *  The entity object we need to validate.
   * @param bool $return_exception
   *  When the validation failed, you can get the the exception object and
   *  handle it your self.
   *
   * @return array|ValidationException
   */
    public function validate(AbstractEntity $entity, bool $return_exception = false)
    {
        $error_list = [];

        try {
            $errors = $this->validator->validate($entity);

            if (!$errors) {
                return [];
            }
        } catch (ValidationException $validationException) {
            if ($return_exception) {
                return $validationException;
            }

            $mappers = $entity->getMapper();

            foreach ($validationException->getConstraintViolationList() as $error) {
                $human_property = $error->getPropertyPath();

                if (!empty($mappers[$error->getPropertyPath()])) {
                    $human_property = $mappers[$error->getPropertyPath()];
                }

                $error_list[$human_property][] = $error->getMessage();
            }
        }

        return $error_list;
    }
}
