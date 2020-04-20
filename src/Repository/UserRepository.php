<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findBestUsers($limit = 2) {
        return $this->createQueryBuilder('u') // on se base sur une entité 'u', mais le repo sait qu'on parle des users
                    ->join('u.ads', 'a') // joindre les annonces de l'utilisateur 
                    ->join('a.comments', 'c') // joindre les commentaires de l'annonce
                    ->select('u as user, AVG(c.rating) as avgRatings, COUNT(c) as sumComments') 
                    // je sélectionne les users et la moyenne des notation des commentaires, et le nombre de commentaires qu'ils ont reçu au total (pour toutes leurs annonces)
                    ->groupBy('u') // je groupe par utilisateur
                    ->having('sumComments >3') 
                    // seuls les utilisateurs qui ont plus de 3 commentaires au total de leurs annonces sont pris en compte
                    ->orderBy('avgRatings', 'DESC') 
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }



    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
