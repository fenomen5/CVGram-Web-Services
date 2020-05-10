<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserType
 *
 * @ORM\Table(name="tbusertype")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserTypeRepository")
 */
class UserType
{
    const JOB_SEEKER = 1;
    const EMPLOYER = 2;
    const ADMINISTRATOR = 3;

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
     * @ORM\Column(name="type_name", type="string", length=20)
     */
    private $typeName;

    /**
     * @var int
     *
     * @ORM\Column(name="admin_role", type="integer", length=1)
     */
    private $adminRole;

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
     * Set typeName.
     *
     * @param int $typeName
     *
     * @return UserType
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * Get typeName.
     *
     * @return int
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @return int
     */
    public function getAdminRole()
    {
        return $this->adminRole;
    }

    /**
     * @param int $adminRole
     */
    public function setAdminRole($adminRole)
    {
        $this->adminRole = $adminRole;
    }


}
