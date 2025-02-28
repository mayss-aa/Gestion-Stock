<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RessourceController extends AbstractController
{
    #[Route('/ressource', name: 'app_ressource_index')]
    public function index(RessourceRepository $ressourceRepository): Response
    {
        return $this->render('GestionStock/Frontoffice/ressource/index.html.twig', [
            'ressources' => $ressourceRepository->findAll(),
        ]);
    }

    #[Route('/ressource/new', name: 'app_ressource_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ressource);
            $entityManager->flush();

            return $this->redirectToRoute('app_ressource_index');
        }

        return $this->render('GestionStock/Frontoffice/ressource/ajout.html.twig', [
            'title' => 'Ajouter une ressource',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ressource/{id}', name: 'app_ressource_show', requirements: ['id' => '\d+'])]
    public function show(int $id, RessourceRepository $ressourceRepository): Response
    {
        $ressource = $ressourceRepository->find($id);
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        return $this->render('GestionStock/Frontoffice/ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/ressource/edit/{id}', name: 'app_ressource_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, RessourceRepository $ressourceRepository, EntityManagerInterface $entityManager): Response
    {
        $ressource = $ressourceRepository->find($id);
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ressource_index');
        }

        return $this->render('GestionStock/Frontoffice/ressource/modifier.html.twig', [
            'title' => 'Modifier la Ressource',
            'ressource' => $ressource,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ressource/delete/{id}', name: 'app_ressource_delete', requirements: ['id' => '\d+'], methods: ['POST', 'DELETE'])]
    public function delete(int $id, RessourceRepository $ressourceRepository, EntityManagerInterface $entityManager): Response
    {
        $ressource = $ressourceRepository->find($id);
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $entityManager->remove($ressource);
        $entityManager->flush();

        return $this->redirectToRoute('app_ressource_index');
    }
}
