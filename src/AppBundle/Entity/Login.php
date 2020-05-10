<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Login
 *
 * @ORM\Table(name="tblogin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LoginRepository")
 */
class Login {
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
     * @ORM\Column(name="login", type="string", length=50)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=250)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="session", type="string")
     */
    private $session;

    /**
     * @var string
     *
     * @ORM\Column(name="expire", type="datetime")
     */
    private $expire;

    /**
     * Login constructor.
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
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
     * Set login.
     *
     * @param string $login
     *
     * @return Login
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Login
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param string $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @param string $expire
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }

    /**
     * Get account fields
     * @return array
     */
    public function getFields()
    {
        return [
            'id' => $this->getId(),
            'login' => $this->getLogin(),
            'session' => $this->getSession()
        ];
    }

}
