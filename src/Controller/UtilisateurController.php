<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\FileUploader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Controller\TwilioSmsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Form\SearchFormType;

class UtilisateurController extends AbstractController
{
    #[Route('/base', name: 'app_base', methods: ['GET'])]
    public function GoToBase(): Response
    {
        return $this->render('utilisateur/index.html.twig');
    }
    #[Route('/front', name: 'app_front', methods: ['GET'])]
    public function GoToFront(): Response
    {
        return $this->render('utilisateur/front.html.twig');
    }
/*
    #[Route('/users',name:'getAll')]
    public function getAll(Request $request,UtilisateurRepository $repo){
        // Get user count per role
        $adminCount = $repo->countByRole('Admin');
        $clientCount = $repo->countByRole('Client');
 // Create and handle the search form
 $searchForm = $this->createForm(SearchFormType::class);
 $searchForm->handleRequest($request);

 // Get search query
 $searchQuery = $searchForm->get('searchQuery')->getData();

 // Get user count per role
 $adminCount = $repo->countByRole('Admin');
 $clientCount = $repo->countByRole('Client');

 // Perform search if the form is submitted and valid
 if ($searchForm->isSubmitted() && $searchForm->isValid()) {
     // Perform search query using $searchQuery
     $users = $repo->findBySearchQuery($searchQuery); // Implement this method in your repository
 } else {
     // If no search query, retrieve all users
     $users = $repo->findAll();
 }

 return $this->render('utilisateur/AfficherUtilisateur.html.twig', [
     'c' => $users,
     'adminCount' => $adminCount,
     'clientCount' => $clientCount,
     'searchForm' => $searchForm->createView(),
 ]);
    }*/
    

    #[Route('/users', name: 'getAll')]
    public function getAll(Request $request, UtilisateurRepository $repo)
    {
        // Get user count per role using the Role entity
        $HommesCount = $repo->countByGender('Homme'); // Assuming this method is updated to count based on Role entity
        $FemmesCount = $repo->countByGender('Femme'); // Same as above
    
        // Create and handle the search form
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
    
        // Get search query
        $searchQuery = $searchForm->get('searchQuery')->getData();
    
        // Perform search if the form is submitted and valid
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Perform search query using $searchQuery
            $users = $repo->findBySearchQuery($searchQuery); // Implement this method in your repository
        } else {
            // If no search query, retrieve all users with their roles
            $users = $repo->findAllWithRoles(); // Use the new method to get users with roles
        }
    
        return $this->render('utilisateur/AfficherUtilisateur.html.twig', [
            'c' => $users,
            'HommesCount' => $HommesCount,
            'FemmesCount' => $FemmesCount, 
            'searchForm' => $searchForm->createView(),
        ]);
    }



    #[Route('user/add', name: 'user_add')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($user);
            //$user->setIsActif(true);
            // Encode the plain password

           /* $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData() // Get the plain password from the form
                )
            );*/
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
            // Hash the password and set it
            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);  // Save the hashed password to the entity
            }
            // Before persisting the user
            error_log("Hashed Password: " . $hashedPassword);

            // Set the role based on the form input
            $user->setRole($form->get('role')->getData()); // Get the selected role from the form
    
            $entityManager->persist($user);
            $entityManager->flush();
            // Do anything else you need here, like send an email
    
      
            $this->addFlash('successmed', 'Utilisateur ajouté avec succès!');
            return $this->redirectToRoute('getAll');
        }

        return $this->render('utilisateur/Ajouter.html.twig', [
            'fc' => $form->createView(),
        ]);
    }




    #[Route('/user/update/{id}', name: 'user_update')]
    public function updateuser(
        $id,
        UtilisateurRepository $repo,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        ParameterBagInterface $parameterBag
    ): Response {
        $user = $repo->find($id);
        if (!$user) {
            $this->addFlash('errormed', 'Utilisateur non trouvé.');
        return $this->redirectToRoute('getAll');
        }
    
        // Create the form and handle the request
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle photo file upload
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $photoDir = $parameterBag->get('image_directory');
                $filename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($photoDir, $filename);
                $user->setPhoto($filename);
            }
    
            // Handle password update
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
    
            // Persist changes to the database
            $entityManager->flush();
    
            $this->addFlash('successmed', 'Utilisateur mis à jour avec succès!');
            // Redirect to a route after successful update
            return $this->redirectToRoute('getAll'); // Change 'getAll' to your actual route name
        }
    
        // Render the form template with the form and user data
        return $this->render('utilisateur/update.html.twig', [
            'fc' => $form->createView(),
            'user' => $user,
        ]);
    }

    
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function deleteuser(ManagerRegistry $manager,UtilisateurRepository $repo,$id){
        $user = $repo->find($id);
        if ($user){     
        $manager->getManager()->remove($user);
        $manager->getManager()->flush();
        $this->addFlash('successmed', 'Utilisateur supprimé avec succès!');
    } else {
        // Error message
        $this->addFlash('errormed', 'Aucun utilisateur trouvé avec cet ID!');
    }
        return $this->redirectToRoute('getAll');
    
        
    }
    
    
    
   


   /* #[Route('/statistics', name: 'user_statistics')]
    public function userStatistics(UtilisateurRepository $userRepository): Response
    {
        // Get user count per role
        $HommesCount = $userRepository->countByGenre('Homme');
        $FemmesCount = $userRepository->countByGenre('Femme');
    
        // Pass the data to the Twig template
        return $this->render('AfficherUtilisateur.html.twig', [
            'HommesCount' => $HommesCount,
            'FemmesCount' => $FemmesCount,
        ]);
    }*/


}











