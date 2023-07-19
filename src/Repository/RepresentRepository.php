<?php

namespace App\Repository;

use App\Entity\Represent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Represent>
 *
 * @method Represent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Represent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Represent[]    findAll()
 * @method Represent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepresentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Represent::class);
    }

//    /**
//     * @return Represent[] Returns an array of Represent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Represent
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
