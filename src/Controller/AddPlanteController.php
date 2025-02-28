<?php

namespace App\Controller;

use App\Entity\Plante;
use App\Form\AddPlanteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\PlanteRepository;

final class AddPlanteController extends AbstractController
{
// AddPlanteController.php

    #[Route('/addplante/{id}', name: 'app_add_plante', defaults: ['id' => null])]
    public function addPlante(Request $request, EntityManagerInterface $entityManager, PlanteRepository $planteRepository, ?int $id): Response
    {
        // Si un ID est fourni, c'est en mode édition (modification), sinon c'est pour ajouter une nouvelle plante
        $plante = $id ? $planteRepository->find($id) : new Plante();

        // Si la plante n'est pas trouvée pour l'édition, rediriger vers la liste des plantes
        if ($id && !$plante) {
            $this->addFlash('error', 'Plante introuvable.');
            return $this->redirectToRoute('app_liste_plante');
        }

        // Création et gestion du formulaire
        $form = $this->createForm(AddPlanteFormType::class, $plante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement dans la base de données
            $entityManager->persist($plante);
            $entityManager->flush();

            // Message de succès et redirection vers la liste des plantes
            $this->addFlash('chaima', $id ? 'Plante modifiée avec succès.' : 'Plante ajoutée avec succès.');
            return $this->redirectToRoute('app_liste_plante'); // Rediriger vers la liste des plantes après soumission
        }

        return $this->render('/Front/plante/add_plante/addplante.html.twig', [
            'AddPlante' => $form->createView(),
            'is_edit' => $id !== null, // Flag pour savoir si on est en mode édition ou ajout
        ]);
    }


    #[Route('/listePlante', name: 'app_liste_plante')]
    public function index(PlanteRepository $planteRepository): Response
    {
        $plantes = $planteRepository->findAll(); // Récupération des plantes depuis la base

        return $this->render('/Front/plante/view_plante/listePlante.html.twig', [
            'plantes' => $plantes, // Passage des données à la vue
        ]);
    }

    #[Route('/plante/supprimer/{id}', name: 'app_plante_supprimer', methods: ['POST'])]
    public function supprimerPlante(int $id, EntityManagerInterface $entityManager): Response
    {   
        $plante = $entityManager->getRepository(Plante::class)->find($id);

        if (!$plante) {
            $this->addFlash('danger', 'Plante introuvable.');
            return $this->redirectToRoute('app_liste_plante');
        }

        $entityManager->remove($plante);
        $entityManager->flush();

        $this->addFlash('chaima', 'Plante supprimée avec succès.');
        return $this->redirectToRoute('app_liste_plante');
    }  



    #[Route('/back/addplante/{id}', name: 'app_add_plante_back', defaults: ['id' => null])]
    public function addPlanteBack(
        Request $request,
        EntityManagerInterface $entityManager,
        PlanteRepository $planteRepository,
        ?int $id
    ): Response {
        // Si un ID est fourni, on récupère la plante pour modification, sinon on en crée une nouvelle
        $plante = $id ? $planteRepository->find($id) : new Plante();

        // Si la plante n'est pas trouvée en mode édition, on redirige vers la liste
        if ($id && !$plante) {
            $this->addFlash('error', 'Plante introuvable.');
            return $this->redirectToRoute('app_liste_plante_back');
        }

        $form = $this->createForm(AddPlanteFormType::class, $plante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plante);
            $entityManager->flush();

            $this->addFlash('chaima', $id ? 'Plante modifiée avec succès.' : 'Plante ajoutée avec succès.');

            return $this->redirectToRoute('app_liste_plante_back'); // Redirection vers la liste des plantes
        }

        return $this->render('Back/Plante/new.html.twig', [
            'AddPlante' => $form->createView(),
            'is_edit' => $id !== null, // Vérifie si c'est une modification ou un ajout
        ]);
    }

    #[Route('/back/listePlante', name: 'app_liste_plante_back')]
    public function indexBack(PlanteRepository $planteRepository): Response
    {
        $plantes = $planteRepository->findAll(); // Récupération des plantes

        return $this->render('Back/Plante/show.html.twig', [
            'plantes' => $plantes,
        ]);
    }

    #[Route('/back/plante/{id}/edit', name: 'app_plante_edit_back', methods: ['GET', 'POST'])]
    public function backedit(
        Request $request,
        Plante $plante,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(AddPlanteFormType::class, $plante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('chaima', 'Plante modifiée avec succès.');
            return $this->redirectToRoute('app_liste_plante_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Back/Plante/edit.html.twig', [
            'plante' => $plante,
            'AddPlante' => $form->createView(),
        ]);
    }

    #[Route('/back/plante/supprimer/{id}', name: 'app_plante_supprimer_back', methods: ['POST'])]
    public function supprimerPlanteBack(int $id, EntityManagerInterface $entityManager): Response
    {
        $plante = $entityManager->getRepository(Plante::class)->find($id);

        if (!$plante) {
            $this->addFlash('danger', 'Plante introuvable.');
            return $this->redirectToRoute('app_liste_plante_back');
        }

        $entityManager->remove($plante);
        $entityManager->flush();

        $this->addFlash('chaima', 'Plante supprimée avec succès.');
        return $this->redirectToRoute('app_liste_plante_back');
    }
}


