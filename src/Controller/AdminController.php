<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findCommandesDuJour();
        $ca        = $commandeRepo->getChiffreAffairesDuJour();

        return $this->render('admin/dashboard.html.twig', [
            'commandes' => $commandes,
            'ca'        => $ca,
            'demoMode'  => false,
        ]);
    }

    #[Route('/admin/commande/{id}/statut', name: 'admin_commande_statut', methods: ['POST'])]
    public function changerStatut(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        $statut = $request->request->get('statut');

        if (in_array($statut, array_values(Commande::STATUTS), true)) {
            $commande->setStatut($statut);
            $em->flush();
            $this->addFlash('success', 'Statut mis à jour.');
        } else {
            $this->addFlash('danger', 'Statut invalide.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}