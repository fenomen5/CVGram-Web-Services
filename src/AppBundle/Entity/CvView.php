<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CvView
 *
 * @ORM\Table(name="tbcvview")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CvViewRepository")
 */
class CvView
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="cv_id", type="integer")
     */
    private $cvId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="viewed_at", type="datetime")
     */
    private $viewedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="employer_id", type="integer")
     */
    private $employerId;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cvId.
     *
     * @param int $cvId
     *
     * @return CvView
     */
    public function setCvId($cvId)
    {
        $this->cvId = $cvId;

        return $this;
    }

    /**
     * Get cvId.
     *
     * @return int
     */
    public function getCvId()
    {
        return $this->cvId;
    }

    /**
     * Set viewedAt.
     *
     * @param \DateTime $viewedAt
     *
     * @return CvView
     */
    public function setViewedAt($viewedAt)
    {
        $this->viewedAt = $viewedAt;

        return $this;
    }

    /**
     * Get viewedAt.
     *
     * @return \DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * Set employerId.
     *
     * @param int $employerId
     *
     * @return CvView
     */
    public function setEmployerId($employerId)
    {
        $this->employerId = $employerId;

        return $this;
    }

    /**
     * Get employerId.
     *
     * @return int
     */
    public function getEmployerId()
    {
        return $this->employerId;
    }
}
