<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /** Afficher les stagiaires non inscrits */
    public function findStagiairesNotSubscribed($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');
        
        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            // requête paramétrée
            ->setParameter('id', $session_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('st.name');
        
        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

    /** Afficher le module lié à la session */
    public function findModuleBySessionId($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        // sélectionner le module d'une session dont l'id est passé en paramètre
        $sub->select('m.title')
            ->from('App\Entity\represent', 'r')
            ->join('r.modules', 'm')
            ->join('r.sessions', 's')
            ->where('s.id = :id')
            ->setParameter('id', $session_id);

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();

    }

    public function findModulesNotProgrammed($session_id) {

        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        $qb->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('m.represents', 'r')
            ->where('r.sessions = :id');
        
        $sub = $em->createQueryBuilder();
        $sub->select('mo')
            ->from('App\Entity\Module', 'mo')
            ->where($sub->expr()->notIn('mo.id', $qb->getDQL()))
            ->setParameter('id', $session_id);
        
        $query = $sub->getQuery();
        return $query->getResult();
    }

    /**
     * @return Session[] Returns an array of Session objects
     */
    public function findSessionsArray(): array
    {
        return $this->createQueryBuilder('sessionArray')
            //->from('App\Entity\Session', 'sessionArray')
            //->andWhere('s.exampleField = :val')
            //->setParameter('val', $value)
            //->orderBy('s.name', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
