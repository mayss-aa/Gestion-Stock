<?php
namespace App\Controller\machinebackoffice;

use App\Entity\Machine;
use App\Form\MachineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/machine')]
class MachineBackofficeController extends AbstractController
{
    #[Route('/list', name: 'backoffice_machine_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $machines = $entityManager->getRepository(Machine::class)->findAll();

        return $this->render('gestion_machine/backoffice/machine/affichage.html.twig', [
            'machines' => $machines,
        ]);
    }

    #[Route('/new', name: 'backoffice_machine_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($machine);
            $entityManager->flush();
            $this->addFlash('successamen', 'Machine ajoutée avec succès !');

            return $this->redirectToRoute('backoffice_machine_list');
        }

        return $this->render('gestion_machine/backoffice/machine/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_machine_edit')]
    public function edit(Request $request, Machine $machine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('successamen', 'Machine mise à jour avec succès !');

            return $this->redirectToRoute('backoffice_machine_list');
        }

        return $this->render('gestion_machine/backoffice/machine/update.html.twig', [
            'form' => $form->createView(),
            'machine' => $machine,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_machine_delete', methods: ['POST'])]
    public function delete(Request $request, Machine $machine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $machine->getId(), $request->request->get('_token'))) {
            $entityManager->remove($machine);
            $entityManager->flush();
            $this->addFlash('successamen', 'Machine supprimée avec succès !');
        }

        return $this->redirectToRoute('backoffice_machine_list');
    }
}
