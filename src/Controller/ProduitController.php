<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ProduitController extends AbstractController
{
    // Front office routes
   
    #[Route('/front/produit', name: 'app_produit_front_index', methods: ['GET'])]
    public function frontindex(ProduitRepository $produitRepository): Response
    {  
return $this->render('Gestion produits/front/produit/index.html.twig', [
    'produits' => $produitRepository->findAll(),
    // Pass the list of produits
        ]);
    }

    #[Route('/front/produit/new', name: 'app_produit_front_new', methods: ['GET', 'POST'])]
    public function frontnew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/front/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/front/produit/{id}', name: 'app_produit_front_show', methods: ['GET'])]
    public function frontshow(Produit $produit): Response
    {
        return $this->render('Gestion produits/front/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/front/produit/{id}/edit', name: 'app_produit_front_edit', methods: ['GET', 'POST'])]
    public function frontedit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/front/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_front_delete', methods: ['POST'])]
    public function frontdelete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_front_index', [], Response::HTTP_SEE_OTHER);
    }
       // Back office routes
       #[Route('/back/produit', name: 'app_produit_back_index', methods: ['GET'])]
     
    public function backtindex(ProduitRepository $produitRepository): Response
    {  
return $this->render('Gestion produits/back/produit/index.html.twig', [
    'produits' => $produitRepository->findAll(),
    // Pass the list of produits
        ]);
    }

    #[Route('/back/produit/new', name: 'app_produit_back_new', methods: ['GET', 'POST'])]
    public function backnew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/back/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/back/produit/{id}', name: 'app_produit_back_show', methods: ['GET'])]
    public function backshow(Produit $produit): Response
    {
        return $this->render('Gestion produits/back/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/back/produit/{id}/edit', name: 'app_produit_back_edit', methods: ['GET', 'POST'])]
    public function backedit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/back/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('back/produit/{id}', name: 'app_produit_back_delete', methods: ['POST'])]
    public function backdelete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_back_index', [], Response::HTTP_SEE_OTHER);
    }
}

