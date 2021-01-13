<?php

namespace App\Repository;

use App\Entity\Ordering;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ordering|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordering|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordering[]    findAll()
 * @method Ordering[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordering::class);
        
    }
    public function findByPayed(){
        return $this->createQueryBuilder('o')
        ->where('o.orderingStatus != :id')
        ->setParameter('id', '0')
        ->orderBy('o.createdAt', 'DESC')
        ->getQuery()
        ->getResult()
        ;
        
    }

    public function findAll(){
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }



    // public function findOneByReference($reference){
    //     return $this->createQueryBuilder('o')
    //         ->andWhere('o.orderingReference = :reference')
    //         ->setParameter('reference', $reference)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //         ;
    // }
    // /**
    //  * @return Ordering[] Returns an array of Ordering objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ordering
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
