<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cv;
use AppBundle\Entity\District;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Login;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\UserCvRepository;
use AppBundle\Service\Account;
use AppBundle\Service\Favorites;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FavoritesController extends Controller
{
    use SecurityTrait;

    /**
     * @Route("/add_favorites", name="add_favorites_page")
     */
    public function indexAction(Request $request)
    {
        $result = $this->checkAccess($request, $this, [UserType::EMPLOYER]);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        $favorites = new Favorites($em);
        $result = $favorites->addFavorites($account->getFields()['profile']['id'], $request->get('cvid'));

        $response = new JsonResponse();

        if ($result) {
            return $response->setContent(json_encode(['success' => true]));
        } else {
            return $response->setContent(json_encode(['data' => 'unable to add to favorites']));
        }
    }

    /**
     * @Route("/favorites", name="employer_favorites_page")
     */
    public function favoritesAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::EMPLOYER);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        $favorites = new Favorites($em);
        $cvIds = $favorites->getFavoritesCvIds($account->getFields()['profile']['id']);

        /** @var UserCvRepository $cvsRepo */
        $cvsRepo = $em->getRepository(Cv::class);
        $cvs = $cvsRepo->findBy([
            'id' => $cvIds
        ]);

        $cvsInfo = [];
        /** @var Cv $cv */
        foreach ($cvs as $cv) {
            $cvsInfo[] = [
                'id' => $cv->getId(),
                'name' => $cv->getName(),
                'region' => $cv->getRegion()->getName(),
                'district' => $cv->getDistrict()->getName(),
                'skills' => $cv->getSkills(),
                'updated' => $cv->getLastEditedAt()->format('d/M H:i'),
                'userId' => $cv->getUserId(),
                'filename' => (!empty($cv->getAttachment())) ? $cv->getAttachment()->getName() : '',
                'sender' => $account->getFields()['profile']['id']
            ];
        }

        return $this->render('default/favorites/favorites.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'cvs' => $cvsInfo
        ]);

    }


}
