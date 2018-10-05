<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\ORM\Mapping\Table as Table;
use App\Entity\AbstractEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Access token entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class AccessToken extends AbstractEntity
{

  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
    public $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
    public $access_token;

  /**
   * @ORM\Column(type="string", length=255)
   */
    public $refresh_token;

  /**
   * @ORM\Column(type="integer")
   */
    public $expires;

  /**
   * @var User
   * @ORM\OneToOne(targetEntity="\App\Entity\User", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=false)
   */
    public $user;
}
