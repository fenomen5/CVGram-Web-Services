<?php

namespace AppBundle\Service;
use AppBundle\Entity\Cv;
use AppBundle\Entity\CvAttachment;
use AppBundle\Entity\CvFile;
use AppBundle\Entity\CvView;
use AppBundle\Entity\District;
use AppBundle\Entity\Login;
use AppBundle\Entity\Profile;
use AppBundle\Entity\Region;
use AppBundle\Entity\UserType;
use AppBundle\Repository\UserCvRepository;
use Distill\Extractor\Util\Filesystem;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Service class for work with user CV
 * Class UserCV
 * @package AppBundle\Service
 */
class UserCV
{
    /** @var \AppBundle\Entity\Cv */
    private $cv;

    /** @var EntityManager */
    private $em;

    /** @var string */
    private $uploadPath;

    public function __construct($em , $uploadPath)
    {
        $this->em = $em;
        $this->uploadPath = $uploadPath;
    }

    /**
     * Save cv in the database and file in filesystem
     * @param $info
     * @return bool
     */
    public function setCvInfo($info)
    {
        $region = $this->em->getRepository(Region::class)->find($info['region']);
        $district = $this->em->getRepository(District::class)->find($info['district']);
        $status = $this->em->getRepository(\AppBundle\Entity\CvStatus::class)->find($info['cvstatus']);
        $industry = $this->em->getRepository(\AppBundle\Entity\Industry::class)->find($info['industry']);

        $this->cv = new Cv(
            $info['title'],
            $info['skills'],
            $region,
            $district,
            $status,
            $industry,
            null,
            $info['user_id'],
            new \DateTime()
        );

        try {
            $this->em->persist($this->cv);
            $this->em->flush();

            if (empty($info['file'])) {
                return true;
            }

            /** @var UploadedFile $file */
            $file = $info['file'];
            $filename = strtotime('now') . $this->cv->getId() . '.' . $file->getClientOriginalExtension();

            $cvAttachment = new CvAttachment();
            $cvAttachment->setName($filename);

            $file->move($this->uploadPath, $filename);

            $this->em->persist($cvAttachment);
            $this->em->flush();

            $this->cv->setAttachment($cvAttachment);

            $this->em->persist($this->cv);
            $this->em->flush();

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function updateCVInfo($info)
    {
        if (empty($info['id'])) {
            return false;
        }

        $cvsRepo = $this->em->getRepository(Cv::class);
        $this->cv = $cvsRepo->find($info['id']);

        if (!$this->cv) {
            return false;
        }

        $region = $this->em->getRepository(Region::class)->find($info['region']);
        $district = $this->em->getRepository(District::class)->find($info['district']);
        $status = $this->em->getRepository(\AppBundle\Entity\CvStatus::class)->find($info['cvstatus']);
        $industry = $this->em->getRepository(\AppBundle\Entity\Industry::class)->find($info['industry']);

        $this->cv->setName($info['title']);
        $this->cv->setSkills($info['skills']);
        $this->cv->setRegion($region);
        $this->cv->setDistrictId($district);
        $this->cv->setStatus($status);
        $this->cv->setClassifier($industry);
        $this->cv->setLastEditedAt(new \DateTime());

        try {
            $this->em->persist($this->cv);
            $this->em->flush();

            if (empty($info['file'])) {
                return true;
            }

            /** @var UploadedFile $file */
            $file = $info['file'];
            $filename = strtotime('now') . $this->cv->getId() . '.' . $file->getClientOriginalExtension();

            if (!$this->cv->getAttachment()) {
                $cvFile = new CvFile();
            } else {
                $cvFile = $this->em
                    ->getRepository(\AppBundle\Entity\CvFile::class)
                    ->find($this->cv->getAttachment());
            }

            $cvFile->setName($filename);

            $file->move($this->uploadPath, $filename);

            $this->em->persist($cvFile);
            $this->em->flush();

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getFields()
    {
        return [
            'industry' => $this->cv->getClassifierId(),
        ];
    }

    /**
     * Remove existing CV from the database
     * @param $cvId
     */
    public function removeCv($cvId)
    {
        if (empty($cvId)) {
            return false;
        }

        $cvsRepo = $this->em->getRepository(Cv::class);
        $this->cv = $cvsRepo->find($cvId);

        if (!$this->cv) {
            return false;
        }

        $filename = $this->cv->getAttachment()->getName();

        try {
            $this->em->remove($this->cv->getAttachment());
            $this->em->remove($this->cv);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        $filesystem->remove($this->uploadPath . $filename);
    }

    /**
     * Add event of viewing the cv by the given user
     * @param $cvId
     * @param $userId
     * @return bool
     */
    public function addCvView($cvId, $userId)
    {
        if (empty($cvId) || empty($userId)) {
            return false;
        }

        $cvView = new CvView();
        $cvView->setCvId($cvId);
        $cvView->setEmployerId($userId);
        $cvView->setViewedAt(new \DateTime());

        try {
            $this->em->persist($cvView);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Set CV status blocked
     * @param int $cvid
     */
    public function blockCv($cvid)
    {
        return  $this->setCvStatus($cvid, \AppBundle\Service\CvStatus::STATUS_BLOCKED);
    }

    /**
     * Set CV status published
     * @param int $cvid
     */
    public function unblockCv($cvid)
    {
       return $this->setCvStatus($cvid, \AppBundle\Service\CvStatus::STATUS_PUBLISHED);
    }

    /**
     * Set a status of the given CV
     * @param $cvid
     * @param $status
     * @return bool
     */
    protected function setCvStatus($cvid, $statusId)
    {
        if (empty($cvid) || empty($statusId)) {
            return false;
        }

        $cvRepo = $this->em->getRepository(Cv::class);

        /** @var Cv $cv */
        $cv = $cvRepo->find($cvid);
        if (empty($cv)) {
            return false;
        }

        $statusRepo = $this->em->getRepository(\AppBundle\Entity\CvStatus::class);
        $status = $statusRepo->find($statusId);

        if (empty($status )) {
            return false;
        }

        $cv->setStatus($status);

        try {
            $this->em->persist($cv);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get id of cv
     * @return int
     */
    public function getcvId()
    {
        return $this->cv->getId();
    }
}
