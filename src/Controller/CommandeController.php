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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commandes')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    // ─── Mes commandes ────────────────────────────────────────────────────────
    #[Route('', name: 'commande_index', methods: ['GET'])]
    public function index(CommandeRepository $repo): Response
    {
        $commandes = $repo->findByClient($this->getUser());

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    // ─── Créer une commande ───────────────────────────────────────────────────
    #[Route('/nouvelle', name: 'commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepo, EntityManagerInterface $em): Response
    {
        $produits = $produitRepo->findDisponibles();

        if ($request->isMethod('POST')) {
            $commande = new Commande();
            $commande->setClient($this->getUser());

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
                return $this->render('commande/new.html.twig', ['produits' => $produits]);
            }

            $em->persist($commande);
            $em->flush();

            $this->addFlash('success', 'Commande passée avec succès !');
            return $this->redirectToRoute('commande_show', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', ['produits' => $produits]);
    }

    // ─── Détail commande ──────────────────────────────────────────────────────
    #[Route('/{id}', name: 'commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        // Sécurité : un client ne peut voir que ses propres commandes
        if ($commande->getClient() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé à cette commande.');
        }

        return $this->render('commande/show.html.twig', ['commande' => $commande]);
    }
}
