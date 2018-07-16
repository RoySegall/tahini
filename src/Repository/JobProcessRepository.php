<?php

namespace App\Repository;

use App\Entity\JobProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * A service for getting complex query results.
 */
class JobProcessRepository extends ServiceEntityRepository {

    /**
     * {@inheritdoc}
     */
    public function __construct(RegistryInterface $registry) {
      $registry->resetManager('default');
      parent::__construct($registry, \App\Entity\Main\JobProcess::class);
    }

    /**
     * Get the query builder for the job process entity.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->createQueryBuilder('jp');
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getMaxJobProcesses($limit = 10) {
        return $this
            ->getQueryBuilder()
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalRows() {
        return $this->getQueryBuilder()
            ->select('count(jp)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}