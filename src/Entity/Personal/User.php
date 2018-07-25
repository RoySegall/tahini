<?php

namespace App\Entity\Personal;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job process entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class User extends AbstractEntity {

  /**
   * @var int The id of this job process.
   *
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer", options={"unsigned":true})
   */
  public $id;

}