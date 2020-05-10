<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cv;
use AppBundle\Entity\CvStatus;
use AppBundle\Entity\CvView;
use AppBundle\Entity\District;
use AppBundle\Entity\Industry;
use AppBundle\Entity\Login;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\UserCvRepository;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    use SecurityTrait;

    /**
     * @Route("/jobseeker/dashboard", name="jobseeker_dashboard_page")
     */
    public function indexAction(Request $request)
    {
        $showErrors = false; $errors = [];
        $email = $request->get('email','');

        $result = $this->checkAccess($request, $this, UserType::JOB_SEEKER);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        $cvsRepo = $em->getRepository(Cv::class);
        $cvs = $cvsRepo->findBy(['userId' => $account->getFields()['profile']['id']]);

        $viewsRepo = $em->getRepository(CvView::class);

        $cvsInfo = [];
        /** @var Cv $cv */
        foreach ($cvs as $cv) {
            $views = $viewsRepo->findBy(['cvId' => $cv->getId()]);
            $cvsInfo[] = [
                'id' => $cv->getId(),
                'name' => $cv->getName(),
                'region' => $cv->getRegion()->getName(),
                'district' => $cv->getDistrict()->getName(),
                'status' => $cv->getStatus()->getName(),
                'updated' => $cv->getLastEditedAt()->format('d/M/Y'),
                'views' => count($views)
            ];
        }

        return $this->render('default/dashboards/jobseeker.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'cvs' => $cvsInfo
        ]);

    }

    /**
     * @Route("/employer/dashboard", name="employer_dashboard_page")
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

        $regionsRepo = $em->getRepository(Region::class);
        $regions = $regionsRepo->findAll();
        $regionsInfo = [];
        /** @var Region $region */
        foreach ($regions as $region) {
            $regionsInfo[] = [
                'id' => $region->getId(),
                'name' => $region->getName()
            ];
        }

        $searchFields = $request->request->all();

        $districtsInfo = [];
        if (!empty($searchFields['district'])) {
            $districtsRepo = $em->getRepository(District::class);
            $districts = $districtsRepo->findBy(['cityId' => $searchFields['region']]);

            /** @var Region $region */
            foreach ($districts as $district) {
                $districtsInfo[] = [
                    'id' => $district->getId(),
                    'name' => $district->getName()
                ];
            }
        }

        $industriesRepo = $em->getRepository(Industry::class);
        $industries = $industriesRepo->findAll();

        $industriesInfo = [];

        /** @var Industry $industry */
        foreach ($industries as $industry) {
            $industriesInfo[] = [
                'id' => $industry->getId(),
                'name' => $industry->getName()
            ];
        }

        /** @var UserCvRepository $cvsRepo */
        $cvsRepo = $em->getRepository(Cv::class);
        $cvs = $cvsRepo->searchCV([
            'cvtitle' => $request->get('cvtitle',''),
            'cvskill' => $request->get('cvskill',''),
            'industry' => $request->get('industry',''),
            'region' => $request->get('region',''),
            'district' => $request->get('district','')
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

        return $this->render('default/dashboards/employer.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'cvs' => $cvsInfo,
            'regions' => $regionsInfo,
            'districts' => $districtsInfo,
            'searchFields' => $searchFields,
            'industries' => $industriesInfo
        ]);

    }

    /**
     * @Route("/administrator/dashboard", name="administrator_dashboard_page")
     */
    public function administratorAction(Request $request)
    {
        $showErrors = false; $errors = [];

        $result = $this->checkAccess($request, $this, UserType::ADMINISTRATOR);
        if ($result) {return $result;}

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $account = new Account($em);
        $account->initFromAuthUser($request);

        $regionsRepo = $em->getRepository(Region::class);
        $regions = $regionsRepo->findAll();
        $regionsInfo = [];
        /** @var Region $region */
        foreach ($regions as $region) {
            $regionsInfo[] = [
                'id' => $region->getId(),
                'name' => $region->getName()
            ];
        }

        $searchFields = $request->request->all();

        $districtsInfo = [];
        if (!empty($searchFields['district'])) {
            $districtsRepo = $em->getRepository(District::class);
            $districts = $districtsRepo->findBy(['cityId' => $searchFields['region']]);

            /** @var Region $region */
            foreach ($districts as $district) {
                $districtsInfo[] = [
                    'id' => $district->getId(),
                    'name' => $district->getName()
                ];
            }
        }

        $industriesRepo = $em->getRepository(Industry::class);
        $industries = $industriesRepo->findAll();

        $industriesInfo = [];

        /** @var Industry $industry */
        foreach ($industries as $industry) {
            $industriesInfo[] = [
                'id' => $industry->getId(),
                'name' => $industry->getName()
            ];
        }

        /** @var UserCvRepository $cvsRepo */
        $cvsRepo = $em->getRepository(Cv::class);
        $cvs = $cvsRepo->searchCV([
            'cvtitle' => $request->get('cvtitle',''),
            'cvskill' => $request->get('cvskill',''),
            'industry' => $request->get('industry',''),
            'region' => $request->get('region',''),
            'district' => $request->get('district',''),
            'ignoreStatus' => true
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
                'blocked' => $cv->getStatus()->getId() == \AppBundle\Service\CvStatus::STATUS_BLOCKED ? true : false,
                'userId' => $cv->getUserId(),
            ];
        }

        return $this->render('default/dashboards/administrator.html.twig', [
            'showErrors' => $showErrors,
            'errors' => $errors,
            'cvs' => $cvsInfo,
            'regions' => $regionsInfo,
            'districts' => $districtsInfo,
            'searchFields' => $searchFields,
            'industries' => $industriesInfo
        ]);

    }


}
