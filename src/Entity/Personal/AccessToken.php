<?php

namespace App\Entity\Personal;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;
use Doctrine\ORM\Mapping\OneToOne as OneToOne;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Access Token entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class AccessToken extends AbstractEntity {

  /**
   * @var int The id of the user.
   *
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer", options={"unsigned":true})
   */
  public $id;

  /**
   * @var integer The user ID.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="integer", nullable=false)
   * @OneToOne(targetEntity="App\Entity\Personal\User")
   */
  public $user;

  /**
   * @var string The access token of the user.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="string", nullable=false)
   */
  public $accessToken;

  /**
   * @var string The refresh token of the user.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="string", nullable=false)
   */
  public $refreshToken;

  /**
   * @var string The description.
   *
   * @Assert\NotNull()
   * @ORM\Column(type="datetime", nullable=true)
   */
  public $expires;

}
