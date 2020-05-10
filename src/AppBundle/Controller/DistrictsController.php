<?php

namespace AppBundle\Controller;

use AppBundle\Entity\District;
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

class DistrictsController extends Controller
{

    use SecurityTrait;

    /**
     * @Route("/districts", name="districts_page")
     */
    public function indexAction(Request $request)
    {
        $result = $this->checkAccess($request, $this, [UserType::JOB_SEEKER, UserType::EMPLOYER]);
        if ($result) {return $result;}

        $showErrors = false; $errors = [];

        $districtsInfo = [];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $districtsRepo = $em->getRepository(District::class);
        $districts = $districtsRepo->findBy(['cityId' => $request->get('city_id')]);

        /** @var District $district */
        foreach ($districts as $district) {
            $districtsInfo[] = [
                'id' => $district->getId(),
                'name' => $district->getName()
            ];
        }

        $response = new JsonResponse();
        return $response->setContent(json_encode(['data' => $districtsInfo]));
    }
}
