<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="register_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('post')) {
            $accountInfo = [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'user_type' => $request->get('user_type'),
                'password' => $request->get('password'),
                'repeat_password' => $request->get('repeat_password'),
            ];
            $validator = new AccountValidator();
            $errors = $validator->validate($accountInfo);

            if (count($errors) > 0) {
                $showErrors = true;
            } else {
                $account = new Account($em);
                $account->setAccountInfo($accountInfo);
                if (!$account->saveAccount($em)) {
                    $errors[] = 'Unable to save user';
                    $showErrors = true;
                } else {
                    return $this->redirect("/login");
                }
            };

        }

        $userTypesRepo = $em->getRepository(UserType::class);
        $usetTypes = $userTypesRepo->findBy(['adminRole' => 0]);

        $usetTypesInfo = [];

        /** @var UserType $userType */
        foreach ($usetTypes as $userType) {
            $usetTypesInfo[] = [
                'id' => $userType->getId(),
                'name' => $userType->getTypeName()
            ];
        }

        // replace this example code with whatever you need
        return $this->render('default/register.html.twig', [
            'user_types' => $usetTypesInfo,
            'showErrors' => $showErrors,
            'errors' => $errors,
            'account' => empty($accountInfo) ? [] : $accountInfo
        ]);
    }
}
