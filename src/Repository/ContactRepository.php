<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * Recherche dans : firstName, lastName, phone, email + champs custom (name/value) + groupes (name)
     * + filtre favoris
     *
     * @return Contact[]
     */
    public function search(?string $q, bool $fav = false): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.fields', 'f')
            ->addSelect('f')
            ->leftJoin('c.groups', 'g')
            ->addSelect('g')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            ->distinct();

        // Filtre favoris
        if ($fav) {
            $qb->andWhere('c.isFavorite = true');
        }

        $q = trim((string) $q);
        if ($q !== '') {
            // Postgres : ILIKE = LIKE insensible à la casse
            $qb->andWhere(
                'c.firstName ILIKE :q OR
                 c.lastName ILIKE :q OR
                 c.phone ILIKE :q OR
                 c.email ILIKE :q OR
                 f.name ILIKE :q OR
                 f.value ILIKE :q OR
                 g.name ILIKE :q'
            )->setParameter('q', '%'.$q.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
