<?php

namespace App\Repository;

use App\Entity\Deal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    public function findAllOrderByDate()
    {
        return $this->findBy(array(), array('startDate' => 'DESC'));
    }

    public function findAllOrderByHot()
    {
        return $this->createQueryBuilder('a')
            ->where("a.rateValue >= 100")
            ->orderBy("a.rateValue", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByXHot(int $x)
    {
        return $this->createQueryBuilder('a')
            ->where("a.rateValue >= 100")
            ->orderBy("a.rateValue", "DESC")
            ->getQuery()
            ->setMaxResults($x)
            ->getResult();
    }

    public function findAllOrderByCommentCount()
    {
        return $this->createQueryBuilder('a')
            ->orderBy("a.commentCount", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function findAllWeeklyDealsOrderByCommentCount()
    {
        $currDate = new \DateTime();
        $currDate->sub(new \DateInterval('P7D'));
        return $this->createQueryBuilder('a')
            ->where('a.startDate > ?1')
            ->orderBy("a.commentCount", "DESC")
            ->setParameter(1, $currDate)
            ->getQuery()
            ->getResult();
    }

    public function findAllByAuthorId(int $userId)
    {
        return $this->createQueryBuilder('d')
            ->where("d.author = ?1")
            ->setParameter(1, $userId)
            ->getQuery()
            ->getResult();
    }

    public function findHotestByAuthorId(int $userId)
    {
        return $this->createQueryBuilder('d')
            ->where("d.author = ?1")
            ->setParameter(1, $userId)
            ->orderBy("d.rateValue", "DESC")
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
    }

    public function findAllOrderByHotAndByAuthorId(int $userId)
    {
        return $this->createQueryBuilder('d')
            ->where("d.rateValue >= 100")
            ->andWhere("d.author = ?1")
            ->setParameter(1, $userId)
            ->orderBy("d.rateValue", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function avgPostedByAuthorIdUnderOneYear(int $userId)
    {
        $lastYear = new \DateTime();
        $lastYear->sub(new \DateInterval('P1Y'));
        return $this->createQueryBuilder('d')
            ->select('AVG(d.rateValue)')
            ->where("d.author = ?1")
            ->andWhere('d.startDate > ?2')
            ->setParameter(1, $userId)
            ->setParameter(2, $lastYear)
            ->getQuery()
            ->getResult();
    }

    public function findWeeklyDealsForAPI()
    {
        $currDate = new \DateTime();
        $currDate->sub(new \DateInterval('P7D'));
        return $this->createQueryBuilder('a')
            ->select('a.title')
            ->where('a.startDate > ?1')
            ->orderBy("a.commentCount", "DESC")
            ->setParameter(1, $currDate)
            ->getQuery()
            ->getResult();
    }

    public function findWeeklyDealsForAPIById($id)
    {
        $currDate = new \DateTime();
        $currDate->sub(new \DateInterval('P7D'));
        return $this->createQueryBuilder('a')
            ->select('a.title')
            ->where('a.startDate > ?1')
            ->andWhere('a.id = :id')
            ->orderBy("a.commentCount", "DESC")
            ->setParameter(1, $currDate)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findSavedDealsByUserForAPI($dealsId)
    {
        $deals = array();
        foreach ($dealsId as $id) {
            $deal = $this->createQueryBuilder('d')
                ->select('d.id, d.title, d.description, d.link')
                ->andWhere('d.id = :userId')
                ->setParameter('userId', $id)
                ->getQuery()
                ->getResult();
            if ($deal != null) {
                array_push($deals, $deal);
            }
        }
        return $deals;
    }

    public function findAllByCriteria($criteria)
    {
        return $this->createQueryBuilder('d')
            ->where('d.title LIKE :criteria')
            ->orWhere('d.description LIKE :criteria')
            ->setParameter('criteria', '%'.$criteria.'%')
            ->getQuery()
            ->getResult();
    }
}
