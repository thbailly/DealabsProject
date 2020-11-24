<?php

namespace App\Repository;

use App\Entity\PromoCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromoCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoCode[]    findAll()
 * @method PromoCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoCode::class);
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
}
