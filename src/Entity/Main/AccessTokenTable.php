<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\ORM\Mapping\Table as Table;
use \App\Entity\Personal\User;
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
class AccessTokenTable extends AbstractEntity {

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
   * @ORM\Column(type="time")
   */
  public $expires;

  /**
   * @ORM\Column(type="integer", length=255)
   * @ORM\JoinColumn(nullable=false)
   */
  public $user;

}
