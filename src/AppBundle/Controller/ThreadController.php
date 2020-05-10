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

class ThreadController extends Controller
{
    use SecurityTrait;

    /**
     * @Route("/thread", name="show_thread_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];
        $email = $request->get('email','');

        $result = $this->checkAccess($request, $this, [UserType::JOB_SEEKER, UserType::EMPLOYER]);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var MessageRepository $messagesRepo */
        $messagesRepo = $em->getRepository(Message::class);
        $messages = $messagesRepo->getUserThread($account->getFields()['profile']['id'], $request->get('receiver'));

        foreach ($messages as $key => $message ) {
            $messages[$key]['date'] = $message['date']->format('d/m/Y H:i');
        }

        $template = $account->isEmployer()
            ? 'default/feedbacks/employerthread.html.twig'
            : 'default/feedbacks/jobseekerthread.html.twig';

        return $this->render($template, [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'messages' => $messages
        ]);
        return new JsonResponse(['ok']);
    }

    /**
     * @Route("/message", name="message_view_page")
     */
    public function viewMessageAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, [UserType::EMPLOYER, UserType::JOB_SEEKER]);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var MessageRepository $messagesRepo */
        $messagesRepo = $em->getRepository(Message::class);

        /** @var Message $message */
        $message = $messagesRepo->getUserMessage($account->getFields()['profile']['id'],$request->get('messageId'));

        if (!$message) {
            return $this->render('default/feedbacks/employermessage.html.twig', [
                'showErrors' => true,
                'errors' => ['message not found'],
                'message' => [],
                'userId' => $account->getFields()['profile']['id']
            ]);
        }

        return $this->render('default/feedbacks/employermessage.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'message' => $message,
            'userId' => $account->getFields()['profile']['id']
        ]);
    }
}
