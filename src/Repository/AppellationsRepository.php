<?php

namespace App\Repository;

use App\Entity\Appellations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Appellations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appellations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appellations[]    findAll()
 * @method Appellations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppellationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appellations::class);
    }

    // /**
    //  * @return Appellations[] Returns an array of Appellations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Appellations
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
