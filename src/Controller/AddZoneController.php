<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Form\AddZoneType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ZoneRepository;

final class AddZoneController extends AbstractController
{   

    ///////Front Controller /////////
    #[Route('/addzone/{id}', name: 'app_add_zone', defaults: ['id' => null])]
    public function addzone(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository, ?int $id): Response
    {
        // Si un ID est fourni, c'est en mode édition (modification), sinon c'est pour ajouter une nouvelle zone
        $zone = $id ? $zoneRepository->find($id) : new Zone();
    
        // Si la zone n'est pas trouvée pour l'édition, rediriger vers la liste des zones
        if ($id && !$zone) {
            $this->addFlash('error', 'Zone introuvable.');
            return $this->redirectToRoute('app_liste_zone');
        }
    
        $form = $this->createForm(AddZoneType::class, $zone);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zone);
            $entityManager->flush();
    
            $this->addFlash('success', $id ? 'Zone modifiée avec succès.' : 'Zone ajoutée avec succès.');
    
            return $this->redirectToRoute('app_liste_zone'); // Redirection vers la liste des zones après soumission
        }
    
        return $this->render('/Front/zone/add_zone/addzone.html.twig', [
            'AddZone' => $form->createView(),
            'is_edit' => $id !== null, // Flag pour savoir si on est en mode édition ou ajout
        ]);
    }
    


    #[Route('/listeZone', name: 'app_liste_zone')]
    public function index(ZoneRepository $zoneRepository): Response
    {
        $zones = $zoneRepository->findAll(); // Récupération des zones depuis la base

        return $this->render('/Front/zone/view_zone/listeZone.html.twig', [
            'zones' => $zones, // Passage des données à la vue
        ]);
    }


    #[Route('/zone/supprimer/{id}', name: 'app_zone_supprimer', methods: ['POST'])]
    public function supprimerZone(int $id, EntityManagerInterface $entityManager): Response
    {   
    $zone = $entityManager->getRepository(Zone::class)->find($id);

    if (!$zone) {
        $this->addFlash('danger', 'Zone introuvable.');
        return $this->redirectToRoute('app_liste_zone');
    }

    $entityManager->remove($zone);
    $entityManager->flush();

    $this->addFlash('success', 'Zone supprimée avec succès.');
    return $this->redirectToRoute('app_liste_zone');
    }





    //////Back Controller //////
       #[Route('/back/addzone/{id}', name: 'app_add_zone_back', defaults: ['id' => null])]
       public function addzoneback(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository, ?int $id): Response
       {
           // Si un ID est fourni, c'est en mode édition (modification), sinon c'est pour ajouter une nouvelle zone
           $zone = $id ? $zoneRepository->find($id) : new Zone();
       
           // Si la zone n'est pas trouvée pour l'édition, rediriger vers la liste des zones
           if ($id && !$zone) {
               $this->addFlash('error', 'Zone introuvable.');
               return $this->redirectToRoute('app_liste_zone_back');
           }
       
           $form = $this->createForm(AddZoneType::class, $zone);
           $form->handleRequest($request);
       
           if ($form->isSubmitted() && $form->isValid()) {
               $entityManager->persist($zone);
               $entityManager->flush();
       
               $this->addFlash('success', $id ? 'Zone modifiée avec succès.' : 'Zone ajoutée avec succès.');
       
               return $this->redirectToRoute('app_liste_zone_back'); // Redirection vers la liste des zones après soumission
           }
       
           return $this->render('/Back/Zone/new.html.twig', [
               'AddZone' => $form->createView(),
               'is_edit' => $id !== null, // Flag pour savoir si on est en mode édition ou ajout
           ]);
       }
       
   
   
       #[Route('/back/listeZone', name: 'app_liste_zone_back')]
       public function indexBack(ZoneRepository $zoneRepository): Response
       {
           $zones = $zoneRepository->findAll(); // Récupération des zones depuis la base
   
           return $this->render('/Back/Zone/show.html.twig', [
               'zones' => $zones, // Passage des données à la vue
           ]);
       }

       #[Route('/back/zone/{id}/edit', name: 'app_zone_edit_back', methods: ['GET', 'POST'])]
       public function backedit(Request $request, Zone $zone, EntityManagerInterface $entityManager): Response
       {
           $form = $this->createForm(AddZoneType::class, $zone);
           $form->handleRequest($request);
   
           if ($form->isSubmitted() && $form->isValid()) {
               $entityManager->flush();
   
               return $this->redirectToRoute('app_liste_zone_back', [], Response::HTTP_SEE_OTHER);
           }
   
           return $this->render('/Back/Zone/edit.html.twig', [
               'zones' => $zone,
               'AddZone' => $form,
           ]);
       }
   
   
       #[Route('/back/zone/supprimer/{id}', name: 'app_zone_supprimer_back', methods: ['POST'])]
       public function supprimerZoneBack(int $id, EntityManagerInterface $entityManager): Response
       {   
       $zone = $entityManager->getRepository(Zone::class)->find($id);
   
       if (!$zone) {
           $this->addFlash('danger', 'Zone introuvable.');
           return $this->redirectToRoute('app_liste_zone_back');
       }
   
       $entityManager->remove($zone);
       $entityManager->flush();
   
       $this->addFlash('success', 'Zone supprimée avec succès.');
       return $this->redirectToRoute('app_liste_zone_back');
       }

}
