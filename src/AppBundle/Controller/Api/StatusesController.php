<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\CvStatus;
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

class StatusesController extends Controller
{
    /**
     * @Route("/api/cvstatuses", name="api_statuses_page")
     */
    public function indexAction(Request $request)
    {
        if ($request->isMethod('post')) {

            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $statusesRepo = $em->getRepository(CvStatus::class);
            $statuses = $statusesRepo->findBy(['adminAccess' => 0]);

            $statusesInfo = [];

            /** @var CvStatus $status */
            foreach ($statuses as $status) {
                $statusesInfo[] = [
                    'id' => $status->getId(),
                    'name' => $status->getName()
                ];
            }

            $jsonResponse = new JsonResponse();
            $jsonResponse->setContent(json_encode([
                'items' => $statusesInfo
            ]));
            $jsonResponse->setStatusCode(Response::HTTP_OK);

            return $jsonResponse;
        }
    }
}
