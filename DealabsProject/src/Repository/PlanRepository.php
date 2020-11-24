<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method Plan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plan[]    findAll()
 * @method Plan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
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
}
