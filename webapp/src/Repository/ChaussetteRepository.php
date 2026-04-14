<?php

namespace App\Repository;

use App\Entity\Chaussette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chaussette>
 */
class ChaussetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chaussette::class);
    }

    public function getFilteredQueryBuilder(array $filters): QueryBuilder
    {
        // 'c' est l'alias pour l'entité Chaussette
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.couleur', 'co') // Jointure vers l'entité Couleur
            ->leftJoin('c.type', 't')     // Jointure vers l'entité Type
            ->orderBy('c.id', 'ASC');     // Tri par défaut (ID)

        // --- FILTRES ---

        // 1. Filtre par date de création
        if (!empty($filters['date_creation'])) {
            // Selon le type de ton champ en base, un LIKE peut fonctionner pour une recherche partielle
            // Si c'est un DateTime exact, il faudra adapter avec 'c.dateCreation = :date'
            $qb->andWhere('c.dateCreation LIKE :date')
                ->setParameter('date', '%' . $filters['date_creation'] . '%');
        }

        // 2. Filtre par nom de la couleur
        if (!empty($filters['couleur'])) {
            $qb->andWhere('co.NomCouleur LIKE :couleur')
                ->setParameter('couleur', '%' . $filters['couleur'] . '%');
        }

        // 3. Filtre sur la notion de couple (true, false, ou ignoré si null)
        if ($filters['couple'] !== null) {
            $qb->andWhere('c.couple = :couple')
                ->setParameter('couple', $filters['couple']);
        }

        // 4. Filtre par taille
        if (!empty($filters['taille'])) {
            $qb->andWhere('c.taille LIKE :taille')
                ->setParameter('taille', '%' . $filters['taille'] . '%');
        }

        // 5. Filtre par nom du type
        if (!empty($filters['type'])) {
            $qb->andWhere('t.NomType LIKE :type')
                ->setParameter('type', '%' . $filters['type'] . '%');
        }

        // Tu peux aussi ajouter un filtre sur le statut si besoin (ex: $c->Statut)
        // if (isset($filters['statut'])) { ... }

        return $qb; // On retourne le QueryBuilder
    }

    /**
     * Récupère la liste des tailles uniques existantes pour les menus déroulants.
     * * @return array
     */
    public function findDistinctTailles(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.taille')
            ->distinct()
            ->where('c.taille IS NOT NULL')
            ->orderBy('c.taille', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
