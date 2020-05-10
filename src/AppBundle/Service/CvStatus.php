<?php

namespace AppBundle\Service;
use AppBundle\Entity\Cv;
use AppBundle\Entity\CvFile;
use AppBundle\Entity\Login;
use AppBundle\Entity\Profile;
use AppBundle\Entity\UserType;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Service class for work with CV statuses
 * Class CvStatus
 * @package AppBundle\Service
 */
class CvStatus
{
    const STATUS_NEW = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DRAFT = 3;
    const STATUS_BLOCKED = 4;
    const STATUS_CLOSED = 5;

}
