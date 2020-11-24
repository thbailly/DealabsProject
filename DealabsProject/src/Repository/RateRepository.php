<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function findByUserIdAndDealId($userId, $dealId)
    {
        return $this->createQueryBuilder('r')
            ->where("r.idUser = ?1")
            ->andWhere("r.idDeal = ?2")
            ->setParameter(1, $userId)
            ->setParameter(2, $dealId)
            ->getQuery()
            ->getResult();
    }
}
