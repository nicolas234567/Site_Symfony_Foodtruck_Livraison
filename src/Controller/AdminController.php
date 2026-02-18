<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(CommandeRepository $repo): Response
    {
        $commandesDuJour = $repo->findCommandesDuJour();
        $ca = $repo->getChiffreAffairesDuJour();

        return $this->render('admin/dashboard.html.twig', [
            'commandes' => $commandesDuJour,
            'ca'        => $ca,
        ]);
    }

    #[Route('/commande/{id}/statut', name: 'admin_commande_statut', methods: ['POST'])]
    public function changerStatut(Request $request, Commande $commande, EntityManagerInterface $em): Response
    {
        $statut = $request->request->get('statut');
        if (in_array($statut, array_values(Commande::STATUTS), true)) {
            $commande->setStatut($statut);
            $em->flush();
            $this->addFlash('success', 'Statut mis à jour.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
