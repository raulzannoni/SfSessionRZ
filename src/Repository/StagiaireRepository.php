<?php

namespace App\Repository;

use App\Entity\Stagiaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stagiaire>
 *
 * @method Stagiaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stagiaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stagiaire[]    findAll()
 * @method Stagiaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StagiaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stagiaire::class);
    }

    /**
     * @return Stagiaire[] Returns an array of Stagiaire objects
     */
    public function findStagiaireArrayInSessionId($session_id): array
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id')
            ->setParameter('id', $session_id)
            ;
        
        $query = $qb->getQuery();
        return $query->getResult();
        
    }

    public function findSessionsNotSubscribed($stagiaire_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les sessions inscrits d'une stagiaire dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->leftJoin('s.stagiaires', 'se')
            ->where('se.id = :id');
        
        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('ses')
            ->from('App\Entity\Session', 'ses')
            ->where($sub->expr()->notIn('ses.id', $qb->getDQL()))
            // requête paramétrée
            ->setParameter('id', $stagiaire_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('ses.name');
        
        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

//    public function findOneBySomeField($value): ?Stagiaire
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
