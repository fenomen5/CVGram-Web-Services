<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\District;
use AppBundle\Entity\Login;
use AppBundle\Entity\Region;
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

class DistrictsController extends Controller
{
    /**
     * @Route("/api/districts", name="api_districts_page")
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('post')) {

            $content = json_decode($request->getContent(), true);

            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $districtsRepo = $em->getRepository(District::class);
            $districts = $districtsRepo->findBy(['cityId' => $content['region_id']]);

            $regionsInfo = [];

            /** @var Region $district */
            foreach ($districts as $district) {
                $regionsInfo[] = [
                    'id' => $district->getId(),
                    'name' => $district->getName()
                ];
            }

            $jsonResponse = new JsonResponse();
            $jsonResponse->setContent(json_encode([
                'items' => $regionsInfo
            ]));
            $jsonResponse->setStatusCode(Response::HTTP_OK);

            return $jsonResponse;
        }
    }
}
