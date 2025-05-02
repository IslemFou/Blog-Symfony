<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    /**
     * @return Articles[] Returns an array of Articles objects
     */
    public function findRecentsAticles(int $limit): array // une méthode personnalisée pour trouver les articles récents
    {
        return $this->createQueryBuilder('a') // Craie une nouvelle requête  DQL (Doctrine Query Language) sur l'entité Articles aliasée par 'a'

            ->orderBy('a.createdAt', 'DESC') // On trie les articles par date de création décroissante
            ->setMaxResults($limit) // On limite le nombre de résultats à $limit
            // tous ce qui est en haut, on peut le modifier pour faire une requête personnalisée 
            ->getQuery() // On exécute la requête avec getQuery() qui convertit le QueryBuilder en un objet Query
            ->getResult() // On récupère le résultat de la requête avec getResult() qui renvoie un tableau d'objets Articles
        ;
    }

    //    public function findOneBySomeField($value): ?Articles
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
