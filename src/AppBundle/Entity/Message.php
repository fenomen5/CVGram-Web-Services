<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="tbmessage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
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
     * @ORM\Column(name="text", type="text")
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime")
     */
    private $sentAt;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="Profile")
     * @ORM\JoinColumn(name="sent_from", referencedColumnName="id")
     */
    private $sentFrom;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="Profile")
     * @ORM\JoinColumn(name="sent_to", referencedColumnName="id")
     */
    private $sentTo;

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
     * Set body.
     *
     * @param string $body
     *
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set sentAt.
     *
     * @param \DateTime $sentAt
     *
     * @return Message
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt.
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set sentFrom.
     *
     * @param int $sentFrom
     *
     * @return Message
     */
    public function setSentFrom($sentFrom)
    {
        $this->sentFrom = $sentFrom;

        return $this;
    }

    /**
     * Get sentFrom.
     *
     * @return int
     */
    public function getSentFrom()
    {
        return $this->sentFrom;
    }

    /**
     * Set sentTo.
     *
     * @param int $sentTo
     *
     * @return Message
     */
    public function setSentTo($sentTo)
    {
        $this->sentTo = $sentTo;

        return $this;
    }

    /**
     * Get sentTo.
     *
     * @return int
     */
    public function getSentTo()
    {
        return $this->sentTo;
    }
}
