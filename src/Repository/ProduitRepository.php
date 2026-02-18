<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findDisponibles(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.disponible = :val')
            ->setParameter('val', true)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
