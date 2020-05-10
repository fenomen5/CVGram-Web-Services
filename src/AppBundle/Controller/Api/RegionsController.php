<?php

namespace AppBundle\Controller\Api;

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

class RegionsController extends Controller
{
    /**
     * @Route("/api/regions", name="api_regions_page")
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('post')) {

            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $regionsRepo = $em->getRepository(Region::class);
            $regions = $regionsRepo->findAll();

            $regionsInfo = [];

            /** @var Region $region */
            foreach ($regions as $region) {
                $regionsInfo[] = [
                    'id' => $region->getId(),
                    'name' => $region->getName()
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
