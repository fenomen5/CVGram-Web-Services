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
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

class RemoveCvController extends Controller
{
    use SecurityTrait;
    /**
     * @Route("/removecv", name="remove_cv_page")
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
        $cv = $cvsRepo->getUserCv($account->getFields()['profile']['id'], $request->get('cvid'));

        if (empty($cv)) {
            return $this->render('default/cvnotfound.html.twig', [
                'link' => 'jobseeker_dashboard_page'
            ]);
        }

        $userCV = new UserCV($em, $this->getParameter('upload_dir'));

        $userCV->removeCv($cv->getId());

        return $this->render('default/cv/cvremoved.html.twig', ['link'=>'jobseeker_dashboard_page']);

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
}
