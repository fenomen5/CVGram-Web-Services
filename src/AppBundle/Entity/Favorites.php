<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Favorites
 *
 * @ORM\Table(name="tbfavorites")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FavoritesRepository")
 */
class Favorites
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
     * @var int
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

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
     * @return Favorites
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return Favorites
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
}
