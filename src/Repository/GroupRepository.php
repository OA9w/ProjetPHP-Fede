<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findAllWithCount(): array
    {
        // Retourne des lignes: ['g' => Group, 'cnt' => int]
        return $this->createQueryBuilder('g')
            ->leftJoin('g.contacts', 'c')
            ->addSelect('COUNT(c.id) AS cnt')
            ->groupBy('g.id')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
