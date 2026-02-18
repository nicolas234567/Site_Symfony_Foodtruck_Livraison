<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findByClient(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.client = :user')
            ->setParameter('user', $user)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Commandes du jour + chiffre d'affaires.
     */
    public function findCommandesDuJour(): array
    {
        $debut = new \DateTime('today');
        $fin   = new \DateTime('tomorrow');

        return $this->createQueryBuilder('c')
            ->andWhere('c.date >= :debut AND c.date < :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getChiffreAffairesDuJour(): float
    {
        $commandes = $this->findCommandesDuJour();
        $total = 0.0;
        foreach ($commandes as $commande) {
            $total += $commande->getTotal();
        }
        return $total;
    }
}
