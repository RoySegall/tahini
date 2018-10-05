<?php

namespace App\Entity;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\ORM\Mapping\OneToOne as OneToOne;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class RoleAssignment extends AbstractEntity
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
   * @ORM\Column(type="integer", nullable=false)
   * @OneToOne(targetEntity="\App\Entity\Personal\Role")
   */
    public $roleId;

  /**
   * @var string The description.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="integer", nullable=false)
   * @OneToOne(targetEntity="\App\Entity\Personal\User")
   */
    public $userId;
}
