<?php
namespace App\Controller;

use App\Entity\Maintenance;
use App\Form\MaintenanceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/maintenance')]
class MaintenanceController extends AbstractController
{
    #[Route('/list', name: 'maintenance_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $maintenances = $entityManager->getRepository(Maintenance::class)->findAll();

        return $this->render('gestion_machine/front/maintenance/index.html.twig', [
            'maintenances' => $maintenances,
        ]);
    }

    #[Route('/new', name: 'maintenance_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maintenance = new Maintenance();
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maintenance);
            $entityManager->flush();

            $this->addFlash('successamen', 'Maintenance ajoutée avec succès !');
            return $this->redirectToRoute('maintenance_list');
        }

        return $this->render('gestion_machine/front/maintenance/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'maintenance_edit')]
    public function edit(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('successamen', 'Maintenance mise à jour avec succès !');

            return $this->redirectToRoute('maintenance_list');
        }

        return $this->render('gestion_machine/front/maintenance/edit.html.twig', [
            'form' => $form->createView(),
            'maintenance' => $maintenance,
        ]);
    }

    #[Route('/{id}/delete', name: 'maintenance_delete', methods: ['POST'])]
    public function delete(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $maintenance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($maintenance);
            $entityManager->flush();
            $this->addFlash('successamen', 'Maintenance supprimée avec succès !');
        }

        return $this->redirectToRoute('maintenance_list');
    }
}


