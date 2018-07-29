<?php

namespace App\Entity\Personal;

use Doctrine\ORM\Mapping as ORM;
use \App\Entity\Personal\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccessTokenRepository")
 */
class AccessToken {

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
   * @ORM\OneToOne(targetEntity="\App\Entity\Personal\User", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=false)
   */
  protected $user;


  /**
   * Getting the user object.
   *
   * @return \App\Entity\Personal\User|null
   */
  public function getUser(): ?User {
    return $this->user;
  }

  /**
   * Setting the user object.
   *
   * @param \App\Entity\Personal\User $user
   * @return AccessToken
   */
  public function setUser(User $user): self {
    $this->user = $user;

    return $this;
  }

}
