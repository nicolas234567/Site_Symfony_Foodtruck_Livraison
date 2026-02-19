<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    // 🔒 Mode démo activé : permet d'afficher le dashboard admin à tout le monde
    private bool $demoMode = true;

    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(CommandeRepository $repo): Response
    {
        // ⚡ Mode démo : simuler un utilisateur admin si aucun n'est connecté
        $user = $this->getUser();
        if ($this->demoMode && !$user) {
            $user = new class {
                private array $roles = ['ROLE_USER','ROLE_ADMIN'];
                public function getRoles() { return $this->roles; }
            };
        }

        // Récupérer toutes les commandes pour le dashboard
        $commandes = $repo->findAll();

        // Calcul du chiffre d'affaires total
        $ca = array_reduce($commandes, fn($total, $c) => $total + $c->getTotal(), 0);

        return $this->render('admin/dashboard.html.twig', [
            'commandes' => $commandes,
            'ca'        => $ca,
            'demoMode'  => $this->demoMode,
            'userRoles' => $user->getRoles(),
        ]);
    }

    #[Route('/commande/{id}/statut', name: 'admin_commande_statut', methods: ['POST'])]
    public function changerStatut(Request $request, Commande $commande, EntityManagerInterface $em): Response
    {
        // ⚡ En mode démo, bloquer la modification
        if ($this->demoMode) {
            $this->addFlash('warning', 'Mode démo : modification du statut impossible.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $statut = $request->request->get('statut');
        if (in_array($statut, array_values(Commande::STATUTS), true)) {
            $commande->setStatut($statut);
            $em->flush();
            $this->addFlash('success', 'Statut mis à jour.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
