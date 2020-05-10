<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends Controller
{
    /**
     * @Route("/api/register", name="api_register_page")
     */
    public function indexAction(Request $request)
    {
        $errors = [];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $utype = new UserType();
        $content = json_decode($request->getContent(), true);

        if ($request->isMethod('post')) {

            $accountInfo = [
                'name' => $content['name'],
                'email' => $content['email'],
                'phone' => $content['phone'],
                'user_type' => $content['user_type'],
                'password' => $content['password'],
                'repeat_password' => $content['repeat_password'],
            ];

            $validator = new AccountValidator();
            $errors = $validator->validate($accountInfo);

            $jsonResponse = new JsonResponse();
            if (count($errors) > 0) {
                $jsonResponse->setStatusCode(Response::HTTP_OK);
                $jsonResponse->setContent(json_encode([
                    'registered' => 'false',
                    'errors' => current($errors)
                ]));

            } else {
                $account = new Account($em);
                $account->setAccountInfo($accountInfo);
                if (!$account->saveAccount($em)) {
                    $errors[] = 'Unable to save user';
                    $jsonResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $jsonResponse->setContent(json_encode([
                        'registered' => 'false',
                        'errors' => print_r($errors,1)
                    ]));

                } else {
                    $jsonResponse->setStatusCode(Response::HTTP_OK);
                    $jsonResponse->setContent(json_encode(['registered' => 'true']));
                }
            };

            return $jsonResponse;
        }
    }
}
