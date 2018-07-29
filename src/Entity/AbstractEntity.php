<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Base entity.
 */
abstract class AbstractEntity {

  protected $mapper = [];

  /**
   * @var boolean When the record has created.
   *
   * @ORM\Column(type="datetime", nullable=true)
   */
  public $created;

  /**
   * @var boolean When the record has been updated.
   *
   * @ORM\Column(type="datetime", nullable=true)
   */
  public $updated;

  /**
   * @return array
   */
  public function getMapper() {
    return $this->mapper;
  }

}
