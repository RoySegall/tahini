<?php

namespace App\Services;

/**
 * Taliaz doctrine service.
 *
 * Since our entities can be located in different DBs, this service wrap for us
 * the way we get the repository of the entity. For example, each time we need
 * to access the job process repo we need to do something like this:
 *  $doctrine->getRepository(\App\Entity\Main\JobProcess::class, 'default')
 *
 * What's the problem? in out case we know that the job process is in the
 * default repo but what about other entities? We need to keep in mind that our
 * code need to be context aware. Pretty sucks :\
 *
 * In order to use this service for get the job process we need to do this:
 *  $taliaz_doctrine->getJobProcessRepository()
 *
 * That's it! pretty easy, no?
 */
class TaliazDoctrine {

  protected $doctrine;

  /**
   * TaliazDoctrine constructor.
   *
   * @param \Doctrine\Common\Persistence\ManagerRegistry $doctrine
   *  The doctrine manager.
   */
  public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $doctrine) {
    $this->doctrine = $doctrine;
  }

  /**
   * @return \Doctrine\Common\Persistence\ObjectRepository
   */
  public function getJobProcessRepository() : \Doctrine\Common\Persistence\ObjectRepository {
    return $this->doctrine->getRepository(\App\Entity\Main\JobProcess::class, 'default');
  }

}