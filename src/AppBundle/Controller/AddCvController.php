<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CvStatus;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\UserCV;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

class AddCvController extends Controller
{
    use SecurityTrait;
    /**
     * @Route("/addcv", name="add_cv_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::JOB_SEEKER);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $userCV = new UserCV($em, $this->getParameter('upload_dir'));

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

        $industriesRepo = $em->getRepository(Industry::class);
        $industries = $industriesRepo->findAll();

        $industriesInfo = [];

        /** @var Industry $industry */
        foreach ($industries as $industry) {
            $industriesInfo[] = [
                'id' => $industry->getId(),
                'name' => $industry->getName()
            ];
        }

        $statusesRepo = $em->getRepository(CvStatus::class);
        $statuses = $statusesRepo->findby(['adminAccess' => 0]);

        $statusesInfo = [];

        /** @var CvStatus $status */
        foreach ($statuses as $status) {
            $statusesInfo[] = [
                'id' => $status->getId(),
                'name' => $status->getName()
            ];
        }
        $userCvInfo = [];
        if ($request->isMethod('post')) {

            $result= $this->validate($request->request->all());

            if (count($result) > 0 ) {
                return $this->render('default/ajaxinvalidform.html.twig', [
                    'errors' => $result,
                ]);
            }

            $account = new Account($em);
            $account->initFromAuthUser($request);
            $userCvInfo = [
                'title' => $request->request->get('title'),
                'industry' => $request->request->get('industry'),
                'skills' => $request->request->get('skills'),
                'region' => $request->request->get('region'),
                'district' => $request->request->get('district'),
                'cvstatus' => $request->request->get('cvstatus'),
                'cvattachment' => $request->request->get('cvattachment'),
                'file' => $request->files->getIterator()->current(),
                'user_id' => $account->getFields()['profile']['id']
            ];

            $userCV->setCvInfo($userCvInfo);
            return (new JsonResponse())->setContent(json_encode(['data' => 'success']));
        }

        return $this->render('default/cv/addcv.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'regions' => $regionsInfo,
            'districts' => [],
            'industries' => $industriesInfo,
            'statuses' => $statusesInfo,
            'usercv' =>  $userCvInfo
        ]);

    }

    /**
     * Validation of the form parameters
     * @param $input
     * @return array
     */
    private function validate($input)
    {
        $constraints = [
            'title' => [new Assert\Length([
                'min' => 5,
                'max' => 240,
                'maxMessage' => 'Title is too long ',
                'minMessage' => 'Title is too short ']),
                new Assert\NotBlank()
            ],
            'industry' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
            ],
            'cvskill' => [
                new Assert\Optional(),
            ],
            'region' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
            ],
            'district' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
            ],
            'skills' => [
                new Assert\NotBlank(),
                new Assert\Type("string")
            ],
            'cvstatus' => [
                new Assert\NotBlank(),
                new Assert\Type("string")
            ],
            'cvattachment' => [
                    new Assert\NotBlank(),
                    new Assert\Type("string")
            ]
        ];

        $validator = Validation::createValidator();

        /** @var ConstraintViolationList $violations */
        $violations = $validator->validate($input, new Assert\Collection($constraints));

        $errors = [];
        if (count($violations) > 0 ) {
            foreach ($violations as $violation)
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
