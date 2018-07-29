<?php

namespace App\Entity\Main;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job process entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class JobProcess extends AbstractEntity {

  /**
   * @var array
   */
  protected $mapper = ['userId' => 'user_id', 'labFile' => 'lab_file', 'questionnaireId' => 'questionnaire_id', 'talResults' => 'tal_results', 'modelRuntime' => 'model_runtime', 'errorMessage' => 'error_message', 'modelBeagle' => 'model_beagle',];

  /**
   * @var int The id of this job process.
   *
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer", options={"unsigned":true})
   */
  public $id;

  /**
   * @var int The user which own the test.
   *
   * @ORM\Column(type="integer", options={"unsigned":true})
   * @Assert\NotNull()
   */
  public $userId;

  /**
   * @var int The file ID.
   *
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  public $labFile;

  /**
   * @var int The questionnaire ID.
   *
   * @ORM\Column(type="integer", options={"unsigned":true})
   * @Assert\NotNull()
   */
  public $questionnaireId;

  /**
   * @var boolean The status of the job.
   *
   * @ORM\Column(type="integer", length=3, options={"unsigned":true})
   */
  public $status;

  /**
   * @var string The tal results 22of the test.
   *
   * @ORM\Column(type="text", nullable=true)
   */
  public $talResults;

  /**
   * @var boolean When the record has updated.
   *
   * @ORM\Column(type="bigint", length=15, nullable=true)
   */
  public $modelRuntime;

  /**
   * @var boolean When the record has updated.
   *
   * @ORM\Column(type="string", length=1)
   *
   * @Assert\NotNull()
   * @Assert\Choice(
   *     choices = {"0", "1"},
   *     message = "The allowed values are '0' or '1'"
   * )
   */
  public $modelBeagle;

  /**
   * @var string The tal results of the test.
   *
   * @ORM\Column(type="string", nullable=true)
   */
  public $errorMessage;

}
