<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commandes')]
class CommandeController extends AbstractController
{
    // 🔒 Mode démo activé : permet de simuler ROLE_ADMIN et tout afficher
    private bool $demoMode = true;

    // ─── Mes commandes ────────────────────────────────────────────────────────
    #[Route('', name: 'commande_index', methods: ['GET'])]
    public function index(CommandeRepository $repo): Response
    {
        $user = $this->getUser();

        if ($this->demoMode && !$user) {
            // Création d'un utilisateur factice pour Twig
            $user = new class {
                private array $roles = ['ROLE_USER','ROLE_ADMIN'];
                public function getRoles() { return $this->roles; }
                public function setRoles(array $roles) { $this->roles = $roles; }
            };
        }

        $commandes = $this->demoMode ? $repo->findAll() : $repo->findByClient($user);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'demoMode' => $this->demoMode,
        ]);
    }

    // ─── Créer une commande ───────────────────────────────────────────────────
    #[Route('/nouvelle', name: 'commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($this->demoMode && !$user) {
            $user = new class {
                private array $roles = ['ROLE_USER','ROLE_ADMIN'];
                public function getRoles() { return $this->roles; }
                public function setRoles(array $roles) { $this->roles = $roles; }
            };
        }

        $produits = $produitRepo->findDisponibles();

        if ($request->isMethod('POST')) {
            $commande = new Commande();
            $commande->setClient($user);

            $quantites = $request->request->all('quantites');

            foreach ($quantites as $produitId => $qte) {
                $qte = (int) $qte;
                if ($qte <= 0) continue;

                $produit = $produitRepo->find($produitId);
                if (!$produit) continue;

                $ligne = new LigneCommande();
                $ligne->setProduit($produit);
                $ligne->setQuantite($qte);
                $commande->addLigneCommande($ligne);
                $em->persist($ligne);
            }

            if ($commande->getLignesCommande()->isEmpty()) {
                $this->addFlash('danger', 'Veuillez choisir au moins un produit.');
                return $this->render('commande/new.html.twig', [
                    'produits' => $produits,
                    'demoMode' => $this->demoMode,
                ]);
            }

            // ⚡ Mode démo : ne flush pas pour ne rien modifier en base
            if (!$this->demoMode) {
                $em->persist($commande);
                $em->flush();
            }

            $this->addFlash('success', 'Commande passée avec succès ! (Mode démo)');
            return $this->redirectToRoute('commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', [
            'produits' => $produits,
            'demoMode' => $this->demoMode,
        ]);
    }

    // ─── Détail commande ──────────────────────────────────────────────────────
    #[Route('/{id}', name: 'commande_show', methods: ['GET'])]
    public function show(CommandeRepository $repo, ?Commande $commande = null): Response
    {
        $user = $this->getUser();

        if ($this->demoMode && !$user) {
            $user = new class {
                private array $roles = ['ROLE_USER','ROLE_ADMIN'];
                public function getRoles() { return $this->roles; }
                public function setRoles(array $roles) { $this->roles = $roles; }
            };
        }

        // ⚡ Mode démo : afficher toutes les commandes
        if ($this->demoMode) {
            $commandes = $repo->findAll();

            return $this->render('commande/show.html.twig', [
                'commande' => $commande ?: $commandes[0] ?? null,
                'toutesCommandes' => $commandes,
                'demoMode' => true,
            ]);
        }

        // Sécurité normale
        if (!$commande || ($commande->getClient() !== $user && !in_array('ROLE_ADMIN', $user->getRoles()))) {
            throw $this->createAccessDeniedException('Accès refusé à cette commande.');
        }

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'demoMode' => false,
        ]);
    }
}
