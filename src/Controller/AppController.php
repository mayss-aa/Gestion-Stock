<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController{
    #[Route('/admin', name: 'app_app')]
    public function index(): Response
    {
        return $this->render('app/app.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
