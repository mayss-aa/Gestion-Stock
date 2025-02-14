<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Form\DepotType;
use App\Repository\DepotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Utilise l'annotation pour les routes

final class DepotController extends AbstractController
{
    // Route pour afficher la liste de tous les dépôts
    #[Route('/depot', name: 'app_depot_index')]
    public function index(DepotRepository $depotRepository): Response
    {
        // On récupère tous les dépôts de la base de données et on les passe à 'index.html.twig'
        return $this->render('depot/index.html.twig', [
            'depots' => $depotRepository->findAll(),
        ]);
    }

    // Route pour afficher la liste des dépôts (route similaire à /depot)
    #[Route('/depot/list', name: 'app_depot_list')]
    public function listDepot(DepotRepository $depotRepository): Response
    {
        // Cette route fait exactement la même chose que 'app_depot_index' et affiche les dépôts dans 'index.html.twig'
        return $this->render('depot/index.html.twig', [
            'depots' => $depotRepository->findAll(),
        ]);
    }

    // Route pour ajouter un nouveau dépôt (création d'un dépôt)
    #[Route('/depot/add', name: 'app_depot_add')]
    public function addDepot(EntityManagerInterface $entityManager): Response
    {
        // Créer un nouvel objet Depot et lui affecter des valeurs par défaut
        $depot = new Depot();
        $depot->setNomDepot('Dépôt Central');
        $depot->setLocalisationDepot('Tunis');
        $depot->setCapaciteDepot(500);
        $depot->setUniteCapDepot('m³');
        $depot->setTypeStockageDepot('Réfrigéré');
        $depot->setStatutDepot('Actif');

        // Enregistrer le dépôt dans la base de données
        $entityManager->persist($depot);
        $entityManager->flush();

        // Retourner une réponse de succès (simple message texte)
        return new Response('Dépôt ajouté avec succès.');
    }

    // Route pour afficher et gérer le formulaire de création d'un nouveau dépôt
    #[Route('/depot/new', name: 'app_depot_new')]
    public function newDepot(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer un objet Depot et un formulaire associé
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on enregistre le dépôt
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($depot);
            $entityManager->flush();

            // Rediriger vers la liste des dépôts après la soumission
            return $this->redirectToRoute('app_depot_list');
        }

        // Retourner la vue 'ajout.html.twig' avec le formulaire
        return $this->render('depot/ajout.html.twig', [
            'title' => 'Ajouter un Dépôt',
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher et gérer le formulaire de modification d'un dépôt
    #[Route('/depot/edit/{id}', name: 'app_depot_edit', requirements: ['id' => '\d+'])]
    public function editDepot(int $id, DepotRepository $depotRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Trouver le dépôt par son ID
        $depot = $depotRepository->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé.');
        }
    
        // Créer un formulaire de modification pour le dépôt
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);
    
        // Si le formulaire est soumis et valide, on met à jour le dépôt
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            // Rediriger vers la liste des dépôts après modification
            return $this->redirectToRoute('app_depot_list');
        }
    
        // Retourner la vue 'modifier.html.twig' avec le formulaire et le dépôt
        return $this->render('depot/modifier.html.twig', [
            'title' => 'Modifier le Dépôt',
            'form' => $form->createView(),
            'depot' => $depot,  // Ajoute ici la variable 'depot'
        ]);
    }
    

    // Route pour supprimer un dépôt
    #[Route('/depot/delete/{id}', name: 'app_depot_delete', requirements: ['id' => '\d+'], methods: ['POST', 'DELETE'])]
    public function deleteDepot(int $id, DepotRepository $depotRepository, EntityManagerInterface $entityManager): Response
    {
        // Trouver le dépôt par son ID
        $depot = $depotRepository->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé.');
        }

        // Supprimer le dépôt de la base de données
        $entityManager->remove($depot);
        $entityManager->flush();

        // Rediriger vers la liste des dépôts après suppression
        return $this->redirectToRoute('app_depot_list');
    }

    // Route pour afficher les détails d'un dépôt spécifique
    #[Route('/depot/{id}', name: 'app_depot_show', requirements: ['id' => '\d+'])]
    public function showDepot(int $id, DepotRepository $depotRepository): Response
    {
        // Trouver le dépôt par son ID
        $depot = $depotRepository->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé.');
        }

        // Retourner la vue 'show.html.twig' avec les détails du dépôt
        return $this->render('depot/show.html.twig', [
            'depot' => $depot,
        ]);
    }
}
