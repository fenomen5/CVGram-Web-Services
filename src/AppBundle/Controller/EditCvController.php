<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cv;
use AppBundle\Entity\CvStatus;
use AppBundle\Entity\District;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\UserCvRepository;
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

class EditCvController extends Controller
{
    use SecurityTrait;
    /**
     * @Route("/editcv", name="edit_cv_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::JOB_SEEKER);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var UserCvRepository $cvsRepo */
        $cvsRepo = $em->getRepository(Cv::class);

        /** @var Cv $cv */
        $cv = $cvsRepo->getUserCV($account->getFields()['profile']['id'], $request->get('cvid'));

        if (empty($cv)) {
            return $this->render('default/cvnotfound.html.twig', [
                'link' => 'jobseeker_dashboard_page'
            ]);
        }

        $userCV = new UserCV($em, $this->getParameter('upload_dir'));
        $userCvInfo = [
            'cvid' => $cv->getId(),
            'cvtitle' => $cv->getName(),
            'region' => $cv->getRegion()->getId(),
            'district' => $cv->getDistrict()->getId(),
            'industry' => $cv->getClassifier()->getId(),
            'cvskills' => $cv->getSkills(),
            'status' => $cv->getStatus()->getId(),
            'attachment' =>  (empty($cv->getAttachment())) ? "" : $cv->getAttachment()->getName()
        ];

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

        $districtsRepo = $em->getRepository(District::class);
        $districts = $districtsRepo->findBy(['cityId' => $cv->getRegion()->getId()]);

        $districtsInfo = [];
        /** @var Region $region */
        foreach ($districts as $district) {
            $districtsInfo[] = [
                'id' => $district->getId(),
                'name' => $district->getName()
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

        if ($request->isMethod('post')) {

            $result= $this->validate($request->request->all());

            if (count($result) > 0 ) {
                return $this->render('default/ajaxinvalidform.html.twig', [
                    'errors' => $result,
                ]);
            }

            $userCvInfo = [
                'id' => $request->request->get('cvid'),
                'title' => $request->request->get('title'),
                'industry' => $request->request->get('industry'),
                'skills' => $request->request->get('skills'),
                'region' => $request->request->get('region'),
                'district' => $request->request->get('district'),
                'cvstatus' => $request->request->get('cvstatus'),
                'cvattachment' => $request->request->get('cvattachment'),
                'file' => $request->files->getIterator()->current(),
                'user_id' => $account->getFields()['profile']['id'],
            ];

            $userCV->updateCVInfo($userCvInfo);
            return (new JsonResponse())->setContent(json_encode(['data' => 'success']));
        }

        return $this->render('default/cv/editcv.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'regions' => $regionsInfo,
            'districts' => $districtsInfo,
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
            'cvid' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric"),
            ],
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
                new Assert\Type("string"),
                new Assert\Choice([
                    'choices' => [
                        \AppBundle\Service\CvStatus::STATUS_DRAFT,
                        \AppBundle\Service\CvStatus::STATUS_PUBLISHED,
                        \AppBundle\Service\CvStatus::STATUS_CLOSED
                    ]
                ])
            ],
            'cvattachment' => [
                    new Assert\Optional(new Assert\Type("string"))
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

    /**
     * @Route("/blockcv", name="block_cv_page")
     */
    public function blockCv(Request $request)
    {
        return $this->blockUnblockCv($request, true);
    }

    /**
     * @Route("/unblockcv", name="unblock_cv_page")
     */
    public function unblockCv(Request $request)
    {
        return $this->blockUnblockCv($request, false);
    }

    /**
     * Set block status for the given CV or changes it to published
     * @param Request $request
     * @param bool $block
     * @return bool|JsonResponse
     */
    protected function blockUnblockCv(Request $request, $block = true)
    {
        $result = $this->checkAccess($request, $this, UserType::ADMINISTRATOR);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var UserCV $userCv */
        $userCv = new UserCV($em, $this->getParameter('upload_dir'));

        if ($block) {
            $result = $userCv->blockCv($request->get('cvid'));
        } else {
            $result = $userCv->unBlockCv($request->get('cvid'));

        }

        $response = new JsonResponse();

        if ($result) {
            return $response->setContent(json_encode(['success' => true]));
        } else {
            return $response->setContent(json_encode(['data' => 'unable to block CV']));
        }
    }
}
