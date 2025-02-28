<?php
namespace App\Controller\machinebackoffice;

use App\Entity\Maintenance;
use App\Form\MaintenanceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/maintenance')]
class MaintenanceBackofficeController extends AbstractController
{
    #[Route('/list', name: 'backoffice_maintenance_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $maintenances = $entityManager->getRepository(Maintenance::class)->findAll();

        return $this->render('gestion_machine/backoffice/maintenance/affichage.html.twig', [
            'maintenances' => $maintenances,
        ]);
    }

    #[Route('/new', name: 'backoffice_maintenance_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maintenance = new Maintenance();
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maintenance);
            $entityManager->flush();
            $this->addFlash('successamen', 'Maintenance ajoutée avec succès !');

            return $this->redirectToRoute('backoffice_maintenance_list');
        }

        return $this->render('gestion_machine/backoffice/maintenance/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_maintenance_edit')]
    public function edit(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('successamen', 'Maintenance mise à jour avec succès !');

            return $this->redirectToRoute('backoffice_maintenance_list');
        }

        return $this->render('gestion_machine/backoffice/maintenance/update.html.twig', [
            'form' => $form->createView(),
            'maintenance' => $maintenance,
        ]);
    }

    // Route pour supprimer une maintenance
        #[Route('/backoffice/maintenance/{id}/delete', name: 'backoffice_maintenance_delete', methods: ['POST'])]
        public function delete(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
        {
    // Vérification du token CSRF
    if ($this->isCsrfTokenValid('delete' . $maintenance->getId(), $request->request->get('_token'))) {
        $entityManager->remove($maintenance);
        $entityManager->flush();
        $this->addFlash('successamen', 'Maintenance supprimée avec succès !');
    }

    return $this->redirectToRoute('gestion_machine/backoffice_maintenance_list'); // Redirection vers la liste des maintenances
}

}