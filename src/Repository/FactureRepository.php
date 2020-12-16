<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    

    /**
    * @return Facture Returns Facture objects
    */
    public function findLastFacture($userId)
    {
        $date = $this->createQueryBuilder('f')
                    ->select('MAX(f.createdAt)')
                    ->andWhere('f.userId = :userId')
                    ->setParameter('userId', $userId)
                    ->getQuery()
                    ->getSingleScalarResult();
    

        return $this->createQueryBuilder('f')
            ->andWhere('f.userId = :userId')
            ->setParameter('userId', $userId)
            ->andWhere('f.createdAt = :date') 
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Facture
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
