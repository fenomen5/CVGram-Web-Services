<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Login;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends Controller
{
    /**
     * @Route("/api/login", name="api_login_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];

        if ($request->isMethod('post')) {

            $account = new Account($this->getDoctrine()->getManager());

            $content = json_decode($request->getContent(), true);
            if (!empty($content['login']) && !empty($content['password'])) {
                $result = $account->initByLoginAndPassword(
                    $content['login'],
                    $content['password']
                );
            } else {
                $result = false;
            }

            $jsonResponse = new JsonResponse();
            if ($result) {
                $token = $account->generateToken();
                $jsonResponse->setContent(json_encode([
                    'token' => $token,
                    'user_type' => $account->getFields()['profile']['user_type'],
                    'user_id' => $account->getFields()['profile']['id']
                ]));
                $jsonResponse->setStatusCode(Response::HTTP_OK);
            } else {
                $jsonResponse->setContent(json_encode([
                    'error' => 'Incorrect login or password'
                ]));
                $jsonResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $jsonResponse;
        }
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
