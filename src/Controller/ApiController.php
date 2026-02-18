<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class ApiController extends AbstractController
{
    /**
     * Route : GET /api/produits
     * Retourne la liste des produits disponibles en JSON.
     */
    #[Route('/produits', name: 'api_produits', methods: ['GET'])]
    public function produits(ProduitRepository $repo): JsonResponse
    {
        $produits = $repo->findDisponibles();

        $data = array_map(fn($p) => [
            'id'          => $p->getId(),
            'nom'         => $p->getNom(),
            'prix'        => $p->getPrix(),
            'description' => $p->getDescription(),
        ], $produits);

        return $this->json($data);
    }

    /**
     * Route : GET /api/commandes/jour
     * Retourne les commandes du jour + CA (admin uniquement).
     */
    #[Route('/commandes/jour', name: 'api_commandes_jour', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function commandesDuJour(CommandeRepository $repo): JsonResponse
    {
        $commandes = $repo->findCommandesDuJour();
        $ca = $repo->getChiffreAffairesDuJour();

        $data = [
            'date'               => (new \DateTime())->format('Y-m-d'),
            'chiffre_affaires'   => round($ca, 2),
            'nombre_commandes'   => count($commandes),
            'commandes'          => array_map(fn($c) => [
                'id'     => $c->getId(),
                'client' => $c->getClient()->getEmail(),
                'statut' => $c->getStatut(),
                'total'  => round($c->getTotal(), 2),
                'date'   => $c->getDate()->format('H:i'),
                'lignes' => array_map(fn($l) => [
                    'produit'  => $l->getProduit()->getNom(),
                    'quantite' => $l->getQuantite(),
                    'sous_total' => round($l->getSousTotal(), 2),
                ], $c->getLignesCommande()->toArray()),
            ], $commandes),
        ];

        return $this->json($data);
    }
}
