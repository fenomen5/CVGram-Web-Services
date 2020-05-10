<?php

namespace AppBundle\Controller;

use AppBundle\Entity\District;
use AppBundle\Entity\Login;
use AppBundle\Entity\UserType;
use AppBundle\Service\Account;
use AppBundle\Service\Validator\AccountValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileDownloadController extends Controller
{

    use SecurityTrait;

    /**
     * @Route("/file_download", name="file_download_page")
     */
    public function indexAction(Request $request)
    {
        $result = $this->checkAccess($request, $this, [UserType::JOB_SEEKER, UserType::EMPLOYER]);
        if ($result) {return $result;}

        $filename = $request->get("filename");
        $filePath = $this->getParameter('upload_dir').'/'.$filename;
        try {
            if (file_exists($filePath)) {
                return $this->file($filePath);
            }
        } catch (\Exception $e) {

        }

        $response = new JsonResponse();
        return $response->setContent(json_encode(['data' => 'File not found']));
    }
}
