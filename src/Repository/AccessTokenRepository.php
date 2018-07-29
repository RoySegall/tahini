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
    $registry->resetManager('personal');
    parent::__construct($registry, \App\Entity\Personal\AccessToken::class);
  }

}
