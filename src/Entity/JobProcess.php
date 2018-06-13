<?php
// api/src/Entity/Book.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Job process entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class JobProcess {

    /**
     * @var int The id of this book.
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
     * @var boolean When the record has created.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $created;

    /**
     * @var boolean When the record has updated.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $updated;

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
     */
    private $modelBeagle;

    /**
     * @var string The tal results of the test.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    public $errorMessage;

    /**
     * Set the beagle.
     *
     * According to doctrine, ENUM are not something we should use. Instead, we
     * can limit the values within the setter.
     *
     * @param $beagle
     *  The beagle value. Allowed values - 0 or 1.
     */
    public function setModelBeagle($beagle) {
        if (!in_array($beagle, array(0, 1))) {
            throw new \InvalidArgumentException("Invalid beagle");
        }

        $this->modelBeagle = $beagle;
    }

    /**
     * Get the beagle mode.
     *
     * @return int
     *  Return 0 or 1.
     */
    public function getModelBeagle() {
        return $this->modelBeagle;
    }

}