<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 */
class AccessTokenRepository extends ServiceEntityRepository {

  /**
   * {@inheritdoc}
   */
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, \App\Entity\Main\AccessTokenTable::class);
  }

}
