<?php

namespace AppBundle\Service;
use AppBundle\Entity\Cv;
use AppBundle\Entity\CvAttachment;
use AppBundle\Entity\CvFile;
use AppBundle\Entity\District;
use AppBundle\Entity\Login;
use AppBundle\Entity\Profile;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use Distill\Extractor\Util\Filesystem;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Service class for work with user favorites
 * Class Favorites
 * @package AppBundle\Service
 */
class Favorites
{
    /** @var EntityManager */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Add cv to favorites of the given user
     * @param int $userId
     * @param int $cvId
     * @return bool
     */
    public function addFavorites($userId, $cvId)
    {
        if (empty($userId) || empty($cvId)) {
            return false;
        }

        $favorites = $this->em->getRepository(\AppBundle\Entity\Favorites::class);
        $favoritesItems = $favorites->findBy([
           'cvId' => $cvId,
            'userId' => $userId
        ]);

        if (count($favoritesItems) > 0) {
            return true;
        }

        /** @var \AppBundle\Entity\Favorites $favoritesItem */
        $favoritesItem = new \AppBundle\Entity\Favorites();
        $favoritesItem->setCvId($cvId);
        $favoritesItem->setUserId($userId);

        try {
            $this->em->persist($favoritesItem);
            $this->em->flush();

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get favorites cvs ids of the given user
     */
    public function getFavoritesCvIds($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $favorites = $this->em->getRepository(\AppBundle\Entity\Favorites::class);
        /** @var \AppBundle\Entity\Favorites[] $favoritesItems */
        $favoritesItems = $favorites->findBy([
            'userId' => $userId
        ]);


        $cvIds = [];
        if (count($favoritesItems) > 0) {
            foreach ($favoritesItems as $favoritesItem) {
                $cvIds[] = $favoritesItem->getCvId();
            }
            return $cvIds;
        }

        return [];
    }
}
