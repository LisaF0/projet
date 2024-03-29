<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    public function findMostSold(){

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            "SELECT p.name, COUNT(p.id)
            FROM App\Entity\Product p, App\Entity\ProductOrdering po
            WHERE p = po.product
            GROUP BY p.id
            HAVING COUNT(p.id) >= ALL(SELECT COUNT(p1.id)
                                        FROM App\Entity\Product p1, App\Entity\ProductOrdering po1
                                        WHERE p1 = po1.product
                                        GROUP BY p1.id)"
        );
        return $query->execute();
    }


    public function findByFilterAndActivate($filter)
    {
        $query =  $this->createQueryBuilder('p')
            ->select('a', 'p', 'd', 't')
            ->andWhere('p.activate = :activate')
            ->setParameter('activate', 1)
            ->join('p.appellation', 'a')
            ->join('p.domain', 'd')
            ->join('p.type', 't')
            ->orderBy('p.name')
        ;

        if(!empty($filter->appellations)){
            $query = $query
                ->andWhere('a.id IN (:appellations)')
                ->setParameter('appellations', $filter->appellations);
        }
        
        if(!empty($filter->domains)){
            $query = $query
                ->andWhere('d.id IN (:domains)')
                ->setParameter('domains', $filter->domains);
        }

        if(!empty($filter->types)){
            $query = $query
                ->andWhere('t.id IN (:types)')
                ->setParameter('types', $filter->types);
        }
        if(!empty($filter->min)){
            $query = $query
                ->andWhere('p.unitPrice >= :min')
                ->setParameter('min', $filter->min);
        }
        if(!empty($filter->max)){
            $query = $query
                ->andWhere('p.unitPrice <= :max')
                ->setParameter('max', $filter->max);
        }

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $filter->page,
            9
        );
            
    }

    public function findByFilter($filter)
    {
        $query =  $this->createQueryBuilder('p')
            ->select('a', 'p', 'd', 't')
            ->join('p.appellation', 'a')
            ->join('p.domain', 'd')
            ->join('p.type', 't')
        ;

            if(!empty($filter->appellations)){
                $query = $query
                    ->andWhere('a.id IN (:appellations)')
                    ->setParameter('appellations', $filter->appellations);
            }
            
            if(!empty($filter->domains)){
                $query = $query
                    ->andWhere('d.id IN (:domains)')
                    ->setParameter('domains', $filter->domains);
            }

            if(!empty($filter->types)){
                $query = $query
                    ->andWhere('t.id IN (:types)')
                    ->setParameter('types', $filter->types);
            }
            if(!empty($filter->min)){
                $query = $query
                    ->andWhere('p.unitPrice >= :min')
                    ->setParameter('min', $filter->min);
            }
            if(!empty($filter->max)){
                $query = $query
                    ->andWhere('p.unitPrice <= :max')
                    ->setParameter('max', $filter->max);
            }

            // return $query->getQuery()->getResult();
            return $this->paginator->paginate(
                $query,
                $filter->page,
                9
            )
            ;
    }




    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
