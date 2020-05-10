<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Industry;
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

class IndustriesController extends Controller
{
    /**
     * @Route("/api/industries", name="api_industries_page")
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('post')) {

            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $industriesRepo = $em->getRepository(Industry::class);
            $industries = $industriesRepo->findAll();

            $industriesInfo = [];

            /** @var Region $region */
            foreach ($industries as $industry) {
                $industriesInfo[] = [
                    'id' => $industry->getId(),
                    'name' => $industry->getName()
                ];
            }

            $jsonResponse = new JsonResponse();
            $jsonResponse->setContent(json_encode([
                'items' => $industriesInfo
            ]));
            $jsonResponse->setStatusCode(Response::HTTP_OK);

            return $jsonResponse;
        }
    }
}
