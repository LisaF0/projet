<?php

namespace App\Repository;

use App\Entity\ShipAddresses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShipAddresses|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipAddresses|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipAddresses[]    findAll()
 * @method ShipAddresses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipAddressesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipAddresses::class);
    }

    /**
     * @return shipAddresses[] Returns an array of ShipAddresses objects
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
    //  * @return ShipAddresses[] Returns an array of ShipAddresses objects
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
    public function findOneBySomeField($value): ?ShipAddresses
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
