<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CvStatus
 *
 * @ORM\Table(name="tbcvstatus")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CvStatusRepository")
 */
class CvStatus
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
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="admin_access", type="integer", nullable=true)
     */
    private $adminAccess;


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
     * @return CvStatus
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
     * Set adminAccess.
     *
     * @param int|null $adminAccess
     *
     * @return CvStatus
     */
    public function setAdminAccess($adminAccess = null)
    {
        $this->adminAccess = $adminAccess;

        return $this;
    }

    /**
     * Get adminAccess.
     *
     * @return int|null
     */
    public function getAdminAccess()
    {
        return $this->adminAccess;
    }
}
