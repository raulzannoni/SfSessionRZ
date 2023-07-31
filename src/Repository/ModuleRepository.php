<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function findSessionsNotProgrammed($module_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->leftJoin('s.represents', 'r')
            ->where('r.modules = :id');

        $sub = $em->createQueryBuilder();
            // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
            // on obtient donc les stagiaires non inscrits pour une session définie
            $sub->select('ses')
                ->from('App\Entity\Session', 'ses')
                ->where($sub->expr()->notIn('ses.id', $qb->getDQL()))
                // requête paramétrée
                ->setParameter('id', $module_id)
                // trier la liste des stagiaires sur le nom de famille
                ->orderBy('ses.name');
            
            // renvoyer le résultat
            $query = $sub->getQuery();
            return $query->getResult();

    }

//    /**
//     * @return Module[] Returns an array of Module objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Module
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
