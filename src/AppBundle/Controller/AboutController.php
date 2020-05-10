<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Login;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends Controller
{
    /**
     * @Route("/about", name="about_page")
     */
    public function indexAction(Request $request)
    {

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);



        if ($account->isJobSeeker()) {
            $header = 'default/navbars/jobseeker.html.twig';
        } elseif ($account->isEmployer()) {
            $header = 'default/navbars/employer.html.twig';
        } elseif ($account->isAdministrator()) {
            $header = 'default/navbars/administrator.html.twig';;
        }

        return $this->render('default/about.twig', [
            'header' => $header
        ]);

    }
}
