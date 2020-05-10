<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\SecurityTrait;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Login;
use AppBundle\Entity\Message;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\MessageRepository;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadsController extends Controller
{
    use SecurityTrait;
    /**
     * @Route("/api/threads", name="api_threads_page")
     */
    public function indexAction(Request $request)
    {
        $result = $this->checkAccess($request, $this, [UserType::EMPLOYER, UserType::JOB_SEEKER]);
        if ($result) {
            $jsonResponse = new JsonResponse();
            $jsonResponse->setStatusCode(Response::HTTP_OK);
            $jsonResponse->setContent(json_encode(['access_denied' => true]));
            return $jsonResponse;
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var MessageRepository $messagesRepo */
        $messagesRepo = $em->getRepository(Message::class);

        /** @var Message $threads */
        $threads = $messagesRepo->getUserThreads($account->getFields()['profile']['id']);

        $jsonResponse = new JsonResponse();
        $jsonResponse->setContent(json_encode([
            'items' => $threads
        ]));
        $jsonResponse->setStatusCode(Response::HTTP_OK);

        return $jsonResponse;
    }
}
