<?php
namespace App\Controller\Backoffice;

use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/backoffice/ressource')]
final class RessourceBackofficeController extends AbstractController
{
    // Afficher la liste des ressources
    #[Route('/', name: 'backoffice_ressource_index', methods: ['GET'])]
    public function index(RessourceRepository $ressourceRepository): Response
    {
        return $this->render('backoffice/ressource/index.html.twig', [
            'ressources' => $ressourceRepository->findAll(),
        ]);
    }

    // Ajouter une ressource
    #[Route('/new', name: 'backoffice_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ressource);
            $entityManager->flush();

            $this->addFlash('success', 'Ressource ajoutée avec succès.');
            return $this->redirectToRoute('backoffice_ressource_index');
        }

        return $this->render('backoffice/ressource/ajouter.html.twig', [
            'title' => 'Ajouter une Ressource',
            'form' => $form->createView(),
        ]);
    }

    // Afficher les détails d'une ressource
    #[Route('/{id}', name: 'backoffice_ressource_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Ressource $ressource): Response
    {
        return $this->render('backoffice/ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    // Modifier une ressource
    #[Route('/edit/{id}', name: 'backoffice_ressource_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Ressource mise à jour avec succès.');
            return $this->redirectToRoute('backoffice_ressource_index');
        }

        return $this->render('backoffice/ressource/modifier.html.twig', [
            'title' => 'Modifier la Ressource',
            'ressource' => $ressource,
            'form' => $form->createView(),
        ]);
    }

    // Supprimer une ressource
    #[Route('/delete/{id}', name: 'backoffice_ressource_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Ressource $ressource, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('delete_ressource', $request->request->get('_token'));

        if (!$csrfTokenManager->isTokenValid($token)) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('backoffice_ressource_index');
        }

        $entityManager->remove($ressource);
        $entityManager->flush();

        $this->addFlash('success', 'Ressource supprimée avec succès.');
        return $this->redirectToRoute('backoffice_ressource_index');
    }
}
