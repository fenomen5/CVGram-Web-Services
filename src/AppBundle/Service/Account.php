<?php

namespace AppBundle\Service;
use AppBundle\Entity\Login;
use AppBundle\Entity\Profile;
use AppBundle\Entity\UserType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Service class for work with user account
 * Class Account
 * @package AppBundle\Service
 */
class Account
{
    /** @var Profile */
    private $profile;

    /** @var Login */
    private $login;

    /** @var EntityManager */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function setAccountInfo($accountInfo)
    {
        $this->profile = new Profile($accountInfo['name'],
            empty($accountInfo['second_name']) ? $accountInfo['name'] : $accountInfo['second_name'],
            empty($accountInfo['second_name']) ? 0 : $accountInfo['second_name'],
            $accountInfo['phone'],
            $accountInfo['email'],
            $accountInfo['user_type']
        );

        $hashedPassword = $this->hashPassword($accountInfo['email'], $accountInfo['password']);
        $this->login = new Login($accountInfo['email'], $hashedPassword);
    }

    /**
     * Saving user acccount into database
     * @return bool
     */
    public function saveAccount()
    {
        try {
            $this->em->persist($this->profile);
            $this->em->flush();
            $this->login->setUserId($this->profile->getId());
            $this->login->setSession(md5(uniqid(rand(), true)));

            $dt = (new \DateTime());
            $dt->setTimestamp(strtotime('now - 1 hour'));
            $this->login->setExpire($dt);

            $this->em->persist($this->login);
            $this->em->flush();
        } catch (\Exception $e) {
            print $e->getMessage();
            return false;
        }

        return true;
    }

    private function hashPassword($login, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, [
            'salt' => strrev(str_pad($login , 22, '_')),
            'cost' => 12
        ]);

        return $hashedPassword;
    }

    /**
     * Get account by login and user data
     * @param $login
     * @param $password
     * @return bool
     */
    public function initByLoginAndPassword($login, $password)
    {
        $loginsRepo = $this->em->getRepository(Login::class);

        $hashedPassword = $this->hashPassword($login, $password);

        $login = $loginsRepo->findBy([
            'login' => $login,
            'password' => $hashedPassword,
        ]);

        if (count($login) > 0) {

            $this->login = $login[0];
            $profilesRepo = $this->em->getRepository(Profile::class);
            $profile = $profilesRepo->findby([
                'id' => $this->login->getUserId(),
                'status' => Profile::ACTIVE_STATUS
            ]);

            if (count($profile) > 0 ) {
                $this->profile = $profile[0];
            }
        }

        return !empty($this->profile);
    }

    public function isJobSeeker()
    {
        return $this->profile->getUserType() == UserType::JOB_SEEKER;
    }

    public function isEmployer()
    {
        return $this->profile->getUserType() == UserType::EMPLOYER;
    }

    public function isAdministrator()
    {
        return $this->profile->getUserType() == UserType::ADMINISTRATOR;
    }

    public function isUserType($userType)
    {
        return $this->profile->getUserType() == $userType;
    }

    /**
     * Set session for the given user
     * @param Response $response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setSession(Response $response)
    {
        $session = $this->generateToken();
        $cookie = new Cookie('cvses', $session, 0);
        $response->headers->setCookie($cookie);
        $response->sendHeaders();
    }

    /**
     * Token generation
     * @return string
     */
    public function generateToken()
    {
        $session = md5(uniqid(rand(), true));
        $this->login->setSession($session);

        $dt = (new \DateTime());
        $dt->setTimestamp(strtotime('now + 20 hours'));
        $this->login->setExpire($dt);

        $this->em->persist($this->login);
        $this->em->flush();

        return $session;
    }

    /**
     * Remove the session of the given user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function clearSession()
    {
        $dt = (new \DateTime());
        $dt->setTimestamp(strtotime('now - 1 year'));
        $this->login->setExpire($dt);

        $this->em->persist($this->login);
        $this->em->flush();

        return true;
    }

    /**
     * Get Authorized User Account
     */
    public function initFromAuthUser(Request $request)
    {
        $loginsRepo = $this->em->getRepository(Login::class);

        $content = json_decode($request->getContent(), true);
        if (!empty($content['token'])) {
            $session = $content['token'];
        } else {
            $session = $request->cookies->get('cvses');
        }

        if (empty($session)) {
            return false;
        }

        $login = $loginsRepo->findBy([
            'session' => $session
        ]);

        if (count($login) == 0) {
            return false;
        }

        $this->login = $login[0];

        if ($this->login->getExpire() <= new \DateTime()) {
            return false;
        }

        $profilesRepo = $this->em->getRepository(Profile::class);
        $profile = $profilesRepo->findBy([
            'id' => $this->login->getUserId(),
            'status' => Profile::ACTIVE_STATUS
        ]);

        if (count($profile) > 0) {
            $this->profile = $profile[0];
        }

        return !empty($this->profile);
    }

    public function getFields()
    {
        return [
            'login' => $this->login->getFields(),
            'profile' => $this->profile->getFields()
        ];
    }

    /**
     * Set profile status blocked
     * @param int $profileId
     */
    public function blockProfile($profileId)
    {
        return  $this->setProfileStatus($profileId, Profile::BLOCKED_STATUS);
    }

    /**
     * Set profile status active
     * @param int $profile
     */
    public function unblockProfile($profileId)
    {
        return $this->setProfileStatus($profileId, Profile::ACTIVE_STATUS);
    }

    /**
     * Set a status of the given profile
     * @param $profileId
     * @param $status
     * @return bool
     */
    protected function setProfileStatus($profileId, $status)
    {
        if (empty($profileId) || empty($status)) {
            return false;
        }

        $profileRepo = $this->em->getRepository(Profile::class);

        /** @var Profile $profile */
        $profile = $profileRepo->find($profileId);
        if (empty($profile)) {
            return false;
        }

        $profile->setStatus($status);

        try {
            $this->em->persist($profile);
            $this->em->flush();
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }

        return true;
    }
}
