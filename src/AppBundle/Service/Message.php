<?php

namespace AppBundle\Service;

use AppBundle\Entity\Profile;
use Doctrine\ORM\EntityManager;

/**
 * Service class for work with user account
 * Class Account
 * @package AppBundle\Service
 */
class Message
{
    /** @var EntityManager */
    private $em;

    /**
     * Message constructor.
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Save message into db
     * @param $from
     * @param $to
     * @param $message
     * @return bool
     */
    public function saveMessage($from, $to, $body)
    {
        if (empty($from) || empty($to) || empty($body)) {
            return false;
        }

        $profileFrom = $this->em->getRepository(Profile::class)->find($from);
        $profileTo = $this->em->getRepository(Profile::class)->find($to);
        try {
            $message = new \AppBundle\Entity\Message();
            $message->setBody($body);
            $message->setSentAt(new \DateTime());
            $message->setSentFrom($profileFrom);
            $message->setSentTo($profileTo);

            $this->em->persist($message);
            $this->em->flush();
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }

        return true;
    }
}
