<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set user properties
            $user->setIsActif(true);
            $user->setMotDePasse(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData() // Get the plain password from the form
                )
            );
            // Ensure the role is set correctly
            $role = $entityManager->getRepository('App\Entity\Role')->findOneBy(['nom' => 'Agriculteur']);
            $user->setRole($role);

            // Persist the user entity
            $entityManager->persist($user);
            $entityManager->flush();

            // Optionally, send a confirmation email here

            // Redirect to the login page after successful registration
            return $this->redirectToRoute('app_login');
        }

        // Render the registration form view
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
