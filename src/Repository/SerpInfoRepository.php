<?php

namespace App\Repository;

use App\Entity\SerpInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SerpInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SerpInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SerpInfo[]    findAll()
 * @method SerpInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class SerpInfoRepository extends ServiceEntityRepository

{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SerpInfo::class);
    }

    // /**
    //  * @return SerpInfo[] Returns an array of SerpInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SerpInfo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

