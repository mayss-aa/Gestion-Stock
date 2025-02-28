<?php 

namespace App\Controller;

use App\Entity\Machine;
use App\Form\MachineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MachineController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/machine/new', name: 'machine_new')]
    public function new(Request $request): Response
    {
        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($machine);
            $this->entityManager->flush();

            $this->addFlash('successamen', 'Machine ajoutée avec succès !');
            return $this->redirectToRoute('machine_list');
        }

        return $this->render('gestion_machine/front/machine/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/machine/list', name: 'machine_list')]
    public function list(): Response
    {
        $machines = $this->entityManager->getRepository(Machine::class)->findAll();

        return $this->render('gestion_machine/front/machine/list.html.twig', [
            'machines' => $machines,
        ]);
    }

    #[Route('/machine/edit/{id}', name: 'machine_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Machine $machine): Response
    {
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('successamen', 'Machine mise à jour avec succès !');
            return $this->redirectToRoute('machine_list');
        }

        return $this->render('gestion_machine/front/machine/edit.html.twig', [
            'form' => $form->createView(),
            'machine' => $machine,
        ]);
    }

    #[Route('/machine/delete/{id}', name: 'machine_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Machine $machine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $machine->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($machine);
            $this->entityManager->flush();
            $this->addFlash('successamen', 'Machine supprimée avec succès !');
        } 

        return $this->redirectToRoute('machine_list');
    }
}


