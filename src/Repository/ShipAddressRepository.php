<?php

namespace App\Repository;

use App\Entity\ShipAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShipAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipAddress[]    findAll()
 * @method ShipAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipAddress::class);
    }

    /**
     * @return shipAddress[] Returns an array of ShipAddress objects
     */
    public function findByUser($user)
    {
        return $this->createQueryBuilder('sa')
            ->andWhere('sa.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
            
        ;
    }

    // /**
    //  * @return ShipAddress[] Returns an array of ShipAddress objects
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
    public function findOneBySomeField($value): ?ShipAddress
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
