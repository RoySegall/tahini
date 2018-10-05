<?php

namespace App\Entity;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class Role extends AbstractEntity
{

  /**
   * @var int The id of the user.
   *
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer", options={"unsigned":true})
   */
    public $id;

  /**
   * @var string The username.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="string", nullable=false)
   */
    public $name;

  /**
   * @var string The description.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="string", nullable=false)
   */
    public $description;
}
