<?php
namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class RoleController extends AbstractController
{
    #[Route('/roles', name: 'getAllroles')]
    public function index(RoleRepository $roleRepository): Response
    {
        $roles = $roleRepository->findAll();

        return $this->render('role/Afficher.html.twig', [
            'roles' => $roles,
        ]);
    }

    #[Route('/role/add', name: 'role_add')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($role);
            $entityManager->flush();
            $this->addFlash('successrole', 'Role ajouté avec succès!');
            return $this->redirectToRoute('getAllroles');
        }
        
        return $this->render('role/Ajouter.html.twig', [
            'frole' => $form->createView(),
        ]);
    }

    #[Route('/role/{id}/update', name: 'role_update')]
    public function edit(Request $request, Role $role, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('successrole', 'Role mis à jour avec succès!');
            return $this->redirectToRoute('getAllroles');
        }

        return $this->render('role/Modifier.html.twig', [
            'frole' => $form->createView(),
            'role' => $role,
        ]);
    }

 
    #[Route('/role/delete/{id}', name: 'role_delete')]
    public function deleterole(ManagerRegistry $manager,RoleRepository $repo,$id){
        $role = $repo->find($id);
        if ($role){     
        $manager->getManager()->remove($role);
        $manager->getManager()->flush();
        $this->addFlash('successrole', 'Role supprimé avec succès!');
    } else {
        // Error message
        $this->addFlash('errorrole', 'Aucun role trouvé avec cet ID!');
    }
        return $this->redirectToRoute('getAllroles');
    
    }

}
