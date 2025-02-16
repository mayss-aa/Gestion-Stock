<?php


namespace App\Controller\StockBackoffice;


use App\Entity\Depot;
use App\Form\DepotType;
use App\Repository\DepotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;


#[Route('/backoffice/depot')]
final class DepotBackofficeController extends AbstractController
{
    // Afficher la liste des dépôts
    #[Route('/', name: 'backoffice_depot_index', methods: ['GET'])]
    public function index(DepotRepository $depotRepository): Response
    {
        return $this->render('gestionstock/Backoffice/depot/index.html.twig', [
            'depots' => $depotRepository->findAll(),
        ]);
    }


    // Ajouter un dépôt
    #[Route('/new', name: 'backoffice_depot_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($depot);
            $entityManager->flush();


            $this->addFlash('successmay', 'Dépôt ajouté avec succès.');
            return $this->redirectToRoute('backoffice_depot_index');
        }


        return $this->render('gestionstock/Backoffice/depot/ajouter.html.twig', [
            'title' => 'Ajouter un Dépôt',
            'form' => $form->createView(),
        ]);
    }


    // Afficher les détails d'un dépôt
    #[Route('/{id}', name: 'backoffice_depot_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Depot $depot): Response
    {
        return $this->render('gestionstock/Backoffice/depot/show.html.twig', [
            'depot' => $depot,
        ]);
    }


    // Modifier un dépôt
    #[Route('/edit/{id}', name: 'backoffice_depot_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Depot $depot, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();


            $this->addFlash('successmay', 'Dépôt mis à jour avec succès.');
            return $this->redirectToRoute('backoffice_depot_index');
        }


        return $this->render('gestionstock/Backoffice/depot/modifier.html.twig', [
            'title' => 'Modifier le Dépôt',
            'depot' => $depot,
            'form' => $form->createView(),
        ]);
    }


    // Supprimer un dépôt
    #[Route('/{id}/delete', name: 'backoffice_depot_delete', methods: ['POST'])]
    public function delete(Request $request, Depot $depot, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $depot->getId(), $request->request->get('_token'))) {
            $entityManager->remove($depot);
            $entityManager->flush();
            $this->addFlash('successmay', 'Depot supprimée avec succès !');
        }


        return $this->redirectToRoute('backoffice_depot_index');
    }












}




