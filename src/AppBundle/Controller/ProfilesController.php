<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cv;
use AppBundle\Entity\District;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Login;
use AppBundle\Entity\Profile;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\ProfileRepository;
use AppBundle\Repository\UserCvRepository;
use AppBundle\Service\Account;
use AppBundle\Service\Favorites;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilesController extends Controller
{
    use SecurityTrait;

    /**
     * @Route("/profiles", name="profiles_page")
     */
    public function profilesAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::ADMINISTRATOR);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var ProfileRepository $profilesRepo */
        $profilesRepo = $em->getRepository(Profile::class);
        $profiles = $profilesRepo->searchProfiles([
            'firstName' => $request->get('firstName', ''),
            'email' => $request->get('email', ''),
            'phone' => $request->get('phone', ''),
            'ignoreStatus' => true
        ]);

        $profilesInfo = [];
        /** @var Profile $profile */
        foreach ($profiles as $profile) {
            $profilesInfo[] = [
                'id' => $profile->getId(),
                'firstName' => $profile->getFirstName(),
                'email' => $profile->getEmail(),
                'phone' => $profile->getPhone(),
                'userType' => $profile->getUserType(),
                'blocked' => $profile->getStatus() == Profile::BLOCKED_STATUS ? true : false
            ];
        }

        return $this->render('default/profile/profiles.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'searchFields' => $request->request->all(),
            'profiles' => $profilesInfo
        ]);

    }

    /**
     * @Route("/blockprofile", name="block_profile_page")
     */
    public function blockCv(Request $request)
    {
        return $this->blockUnblockProfile($request, true);
    }

    /**
     * @Route("/unblockprofile", name="unblock_profile_page")
     */
    public function unblockCv(Request $request)
    {
        return $this->blockUnblockProfile($request, false);
    }

    /**
     * Set block status for the given profile or changes it to active
     * @param Request $request
     * @param bool $block
     * @return bool|JsonResponse
     */
    protected function blockUnblockProfile(Request $request, $block = true)
    {
        $result = $this->checkAccess($request, $this, UserType::ADMINISTRATOR);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        /** @var Account $profile */
        $profile = new Account($em);

        if ($block) {
            $result = $profile->blockProfile($request->get('profileid'));
        } else {
            $result = $profile->unBlockProfile($request->get('profileid'));
        }

        $response = new JsonResponse();

        if ($result) {
            return $response->setContent(json_encode(['success' => true]));
        } else {
            return $response->setContent(json_encode(['data' => 'unable to block profile']));
        }
    }


}
