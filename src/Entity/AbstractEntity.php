<?php

namespace App\Entity;

/**
 * Base entity.
 */
abstract class AbstractEntity
{

    protected $mapper = [];

  /**
   * @return array
   */
    public function getMapper()
    {
        return $this->mapper;
    }
}
