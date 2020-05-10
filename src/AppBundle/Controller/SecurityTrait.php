<?php

namespace AppBundle\Controller;


use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use Symfony\Component\HttpFoundation\Response;

trait SecurityTrait
{

    public function checkAccess($request, $controller, $userType)
    {
        $account = new Account($controller->getDoctrine()->getManager());
        $dashboard = '';
        if ($account->initFromAuthUser($request)) {

            $dashboard = $account->isEmployer() ? 'employer_dashboard_page' : 'jobseeker_dashboard_page';

            if (is_array($userType)) {
                foreach ($userType as $type) {
                    if ($account->isUserType($type)) {
                        return false;
                    }
                }
            }

            if ($account->isUserType($userType) || $account->isAdministrator()) {
                return false;
            }
        }

        return $controller->render('default/accessdenied.html.twig', [
            'link' => $dashboard
        ]);
    }
}