<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\SecurityTrait;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\UserCV;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class CvController extends Controller
{

    use SecurityTrait;

    /**
     * @Route("/api/cvcreate", name="api_cvcreate_page")
     */
    public function indexAction(Request $request)
    {
        $errors = [];

        $result = $this->checkAccess($request, $this, UserType::JOB_SEEKER);
        if ($result) {
            $jsonResponse = new JsonResponse();
            $jsonResponse->setStatusCode(Response::HTTP_OK);
            $jsonResponse->setContent(json_encode(['access_denied' => true]));
            return $jsonResponse;
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $userCV = new UserCV($em, $this->getParameter('upload_dir'));

        $userCvInfo = [];
        if ($request->isMethod('post')) {

            $content = json_decode($request->getContent(), true);

            $result= $this->validate($content);

            if (count($result) > 0 )
            {
                $jsonResponse = new JsonResponse();
                $jsonResponse->setStatusCode(Response::HTTP_OK);
                $jsonResponse->setContent(json_encode([
                    'errors' => print_r($result,1)
                ]));
                return $jsonResponse;
            }

            $account = new Account($em);
            $account->initFromAuthUser($request);

            $userCvInfo = [
                'title' => $content['title'],
                'industry' => $content['industry_id'],
                'skills' => $content['skills'],
                'region' => $content['region_id'],
                'district' => $content['district_id'],
                'cvstatus' => $content['status'],
                'user_id' => $account->getFields()['profile']['id']
            ];

            if ($userCV->setCvInfo($userCvInfo)) {
                return (new JsonResponse())->setContent(json_encode([
                    'created' => 'true',
                    'cv_id' => $userCV->getcvId(),
                ]));
            } else {
                return (new JsonResponse())->setContent(json_encode([
                    'created' => 'false',
                    'cv_id' => 0,
                ]));
            };


        }

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
            'industry_id' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
            ],
            'skills' => [
                new Assert\Optional(),
            ],
            'region_id' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
            ],
            'district_id' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
            ],
            'token' => new Assert\Optional([
                new Assert\NotBlank(),
                new Assert\Type("string")
            ]),
            'status' => [
                new Assert\NotBlank(),
                new Assert\Type("numeric")
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
