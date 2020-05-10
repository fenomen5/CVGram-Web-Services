<?php

namespace AppBundle\Controller;

use AppBundle\Entity\District;
use AppBundle\Entity\Login;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Message;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SendMessageController extends Controller
{

    use SecurityTrait;

    /**
     * @Route("/send_message", name="send_message_page")
     */
    public function indexAction(Request $request)
    {
        $result = $this->checkAccess($request, $this, [UserType::JOB_SEEKER, UserType::EMPLOYER]);
        if ($result) {return $result;}

        $message = new Message($this->getDoctrine()->getManager());

        $result = $message->saveMessage($request->get('from'),$request->get('to'), $request->get('message'));

        $response = new JsonResponse();

        if ($result) {
            return $response->setContent(json_encode(['success' => true]));
        } else {
            return $response->setContent(json_encode(['data' => 'unable to send message']));
        }
    }
}
