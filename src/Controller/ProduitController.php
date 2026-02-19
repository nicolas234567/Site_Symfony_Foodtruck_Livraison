<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/produits')]
class ProduitController extends AbstractController
{
    private bool $demoMode = true; // 🔒 MODE DEMO ACTIVÉ

    // ─── Liste publique ───────────────────────────────────────────────────────
    #[Route('', name: 'produit_index', methods: ['GET'])]
    public function index(ProduitRepository $repo): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $repo->findAll(),
        ]);
    }

    // ─── CRUD (mode démo accessible) ─────────────────────────────────────────
    #[Route('/nouveau', name: 'produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // ⚡ Mode démo : simuler un admin si personne n'est connecté
        $user = $this->getUser();
        if ($this->demoMode && !$user) {
            $user = new class {
                public function getRoles() { return ['ROLE_ADMIN', 'ROLE_USER']; }
            };
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->demoMode) {
                $this->addFlash('warning', 'Mode démo activé : création simulée.');
                return $this->redirectToRoute('produit_index');
            }

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $nomFichier = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $nomFichier);
                $produit->setImage($nomFichier);
            }

            $em->persist($produit);
            $em->flush();
            $this->addFlash('success', 'Produit créé avec succès !');

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'form'    => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}', name: 'produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', ['produit' => $produit]);
    }

    #[Route('/{id}/modifier', name: 'produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        // ⚡ Mode démo : simuler un admin si personne n'est connecté
        $user = $this->getUser();
        if ($this->demoMode && !$user) {
            $user = new class {
                public function getRoles() { return ['ROLE_ADMIN', 'ROLE_USER']; }
            };
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->demoMode) {
                $this->addFlash('warning', 'Mode démo activé : modification simulée.');
                return $this->redirectToRoute('produit_index');
            }

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $nomFichier = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $nomFichier);
                $produit->setImage($nomFichier);
            }

            $em->flush();
            $this->addFlash('success', 'Produit modifié !');

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'form'    => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {

            if ($this->demoMode) {
                $this->addFlash('warning', 'Mode démo activé : suppression simulée.');
                return $this->redirectToRoute('produit_index');
            }

            $em->remove($produit);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('produit_index');
    }
}
