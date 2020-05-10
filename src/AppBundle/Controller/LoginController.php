<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Login;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];
        $email = $request->get('email','');
	$dbconnected = true;

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
          $em->getConnection()->connect();
          $connected = $em->getConnection()->isConnected();
        } catch (\Throwable $e) {
          $dbconnected = false;
            print '<span style="color: black;background: red;width: 100%;
              display: flex;padding: 20px;text-align: center;">The db connection is not configured correctly.
            Set the correct values in app/parameters.yml</span>';
        }

        if ($request->isMethod('post') && $dbconnected == true) {

            $account = new Account($this->getDoctrine()->getManager());

            $result = $account->initByLoginAndPassword(
                $request->get('login'),
                $request->get('password')
            );

            if ($result) {
                $location = '/';
                if ($account->isJobSeeker()) {
                    $location = 'jobseeker/dashboard';
                } elseif ($account->isEmployer()) {
                    $location = 'employer/dashboard';
                } elseif ($account->isAdministrator()) {
                    $location = 'administrator/dashboard';
                }

                $response = new Response();
                $account->setSession($response);

                return $this->redirect($location);
            } else {
                $showErrors = true;
                $errors['login'] = 'Incorrect user password ot email';
            }
        }

        return $this->render('default/login.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'email' => $email
        ]);

    }

    /**
     * @Route("/logout", name="logout_page")
     */
    public function logoutAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        if ($account->initFromAuthUser($request)) {
            if ($account->clearSession()) {
                return $this->redirect('/login');
            }
        }

        return $this->redirect('/');

    }
}
