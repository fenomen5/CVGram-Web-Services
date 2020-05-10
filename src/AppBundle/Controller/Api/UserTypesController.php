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

class UserTypesController extends Controller
{
    /**
     * @Route("/api/usertypes", name="api_usertypes_page")
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('post')) {

            $content = json_decode($request->getContent(), true);

            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $userTypesRepo = $em->getRepository(UserType::class);
            $usetTypes = $userTypesRepo->findBy(['adminRole' => 0]);

            $usetTypesInfo = [];

            /** @var UserType $userType */
            foreach ($usetTypes as $userType) {
                $usetTypesInfo[] = [
                    'id' => $userType->getId(),
                    'name' => $userType->getTypeName()
                ];
            }

            $jsonResponse = new JsonResponse();
            $jsonResponse->setContent(json_encode([
                'items' => $usetTypesInfo
            ]));
            $jsonResponse->setStatusCode(Response::HTTP_OK);

            return $jsonResponse;
        }
    }
}
