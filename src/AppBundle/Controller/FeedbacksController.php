<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cv;
use AppBundle\Entity\District;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Login;
use AppBundle\Entity\Message;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\MessageRepository;
use AppBundle\Repository\UserCvRepository;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FeedbacksController extends Controller
{
    use SecurityTrait;

    /**
     * @Route("/employer/feedbacks", name="employer_feedbacks_page")
     */
    public function employerAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::EMPLOYER);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var MessageRepository $messagesRepo */
        $messagesRepo = $em->getRepository(Message::class);

        /** @var Message $threads */
        $threads = $messagesRepo->getUserThreads($account->getFields()['profile']['id']);

        return $this->render('default/feedbacks/employer.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'feedbacks' => $threads
        ]);
    }


    /**
     * @Route("/jobseeker/feedbacks", name="jobseeker_feedbacks_page")
     */
    public function jobseekerAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::JOB_SEEKER);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var MessageRepository $messagesRepo */
        $messagesRepo = $em->getRepository(Message::class);

        /** @var Message $threads */
        $threads = $messagesRepo->getUserThreads($account->getFields()['profile']['id']);

        return $this->render('default/feedbacks/jobseeker.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'feedbacks' => $threads
        ]);
    }
}
