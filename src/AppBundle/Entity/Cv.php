<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCv
 *
 * @ORM\Table(name="tbcv")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserCvRepository")
 */
class Cv
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="skills", type="string", length=1000)
     */
    private $skills;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     *
     */
    private $region;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    private $district;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="CvStatus")
     * @ORM\JoinColumn(name="status", referencedColumnName="id")
     */
    private $status;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="Industry")
     * @ORM\JoinColumn(name="classifier_id", referencedColumnName="id")
     */
    private $classifierId;

    /**
     * @var int|null
     * @ORM\OneToOne(targetEntity="CvAttachment")
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id")
     */
    private $attachment;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_edited_at", type="datetime", nullable=true)
     */
    private $lastEditedAt;


    /**
     * Cv constructor.
     * @param string $name
     * @param string $skills
     * @param int $regionId
     * @param int $districtId
     * @param int $status
     * @param int $classifierId
     * @param int|null $attachmentId
     * @param int $userId
     * @param \DateTime|null $lastEditedAt
     */
    public function __construct($name, $skills, $regionId, $districtId, $status, $classifierId, $attachmentId, $userId, \DateTime $lastEditedAt)
    {
        $this->name = $name;
        $this->skills = $skills;
        $this->region = $regionId;
        $this->district = $districtId;
        $this->status = $status;
        $this->classifierId = $classifierId;
        $this->attachment = $attachmentId;
        $this->userId = $userId;
        $this->lastEditedAt = $lastEditedAt;
    }


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
     * Set name.
     *
     * @param string $name
     *
     * @return Cv
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Cv
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set classifierId.
     *
     * @param int $classifierId
     *
     * @return Cv
     */
    public function setClassifier($classifierId)
    {
        $this->classifierId = $classifierId;

        return $this;
    }

    /**
     * Get classifierId.
     *
     * @return int
     */
    public function getClassifier()
    {
        return $this->classifierId;
    }

    /**
     * Set attachmentId.
     *
     * @param int|null $attachmentId
     *
     * @return Cv
     */
    public function setAttachment($attachmentId = null)
    {
        $this->attachment = $attachmentId;

        return $this;
    }

    /**
     * Get attachmentId.
     *
     * @return int|null|object
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return Cv
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set lastEditedAt.
     *
     * @param \DateTime|null $lastEditedAt
     *
     * @return Cv
     */
    public function setLastEditedAt($lastEditedAt = null)
    {
        $this->lastEditedAt = $lastEditedAt;

        return $this;
    }

    /**
     * Get lastEditedAt.
     *
     * @return \DateTime|null
     */
    public function getLastEditedAt()
    {
        return $this->lastEditedAt;
    }

    /**
     * @return string
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param string $skills
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
    }

    /**
     * @param int $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param int $district
     */
    public function setDistrictId($district)
    {
        $this->district = $district;
    }

    /**
     * @param int $classifierId
     */
    public function setClassifierId($classifierId)
    {
        $this->classifierId = $classifierId;
    }


//    /*/**
//     * Get set of entity fields
//     */
//    public function getFields()
//    {
//        return [
//           'id' =>
//           'name' => $name
//        ]
//    }*/
}
