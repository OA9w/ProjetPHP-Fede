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

        if ($fav) {
            $qb->andWhere('c.isFavorite = true');
        }

        $q = trim((string)$q);

        if ($q !== '') {
            $qb->andWhere(
                $qb->expr()->orX(
                    'LOWER(c.firstName) LIKE LOWER(:q)',
                    'LOWER(c.lastName) LIKE LOWER(:q)',
                    'LOWER(c.phone) LIKE LOWER(:q)',
                    'LOWER(c.email) LIKE LOWER(:q)',
                    'LOWER(f.name) LIKE LOWER(:q)',
                    'LOWER(f.value) LIKE LOWER(:q)',
                    'LOWER(g.name) LIKE LOWER(:q)'
                )
            )
                ->setParameter('q', '%' . $q . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
