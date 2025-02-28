<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    // Front office routes
    #[Route('/front/categorie', name: 'app_categorie_front_index', methods: ['GET'])]
    public function frontIndex(CategorieRepository $categorieRepository): Response
    {
        return $this->render('Gestion produits/front/categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/front/categorie/new', name: 'app_categorie_front_new', methods: ['GET', 'POST'])]
    public function frontNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/front/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/front/categorie/{nom_categorie}', name: 'app_categorie_front_show', methods: ['GET'])]
    public function frontShow(Categorie $categorie): Response
    {
        return $this->render('Gestion produits/front/categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/front/categorie/{nom_categorie}/edit', name: 'app_categorie_front_edit', methods: ['GET', 'POST'])]
    public function frontEdit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/front/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }
    #[Route('/categorie/{nom_categorie}', name: 'app_categorie_front_delete', methods: ['POST'])]
    public function frontdelete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getNomCategorie(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_front_index', [], Response::HTTP_SEE_OTHER);
    }

    // Back office routes
    #[Route('/back/categorie', name: 'app_categorie_back_index', methods: ['GET'])]
    public function backIndex(CategorieRepository $categorieRepository): Response
    {
        return $this->render('Gestion produits/back/categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }
     // backNew
    #[Route('/back/categorie/new', name: 'app_categorie_back_new', methods: ['GET', 'POST'])]
    public function backNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/back/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }
   // backShow
   
      // backEdit
    #[Route('/back/categorie/{nom_categorie}/edit', name: 'app_categorie_back_edit', methods: ['GET', 'POST'])]
    public function backEdit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion produits/back/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Common backdelete route
   
    #[Route('back/categorie/{nom_categorie}/delete', name: 'app_categorie_back_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' .$categorie->getNomCategorie(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
            $this->addFlash('successahlem', 'categorie supprimée avec succès !');
        }


        return $this->redirectToRoute('app_categorie_back_index');
    }

}
