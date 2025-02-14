<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
         ]);
    }
    #[Route('', name: 'app_homex')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig', [
         ]);
    }
}
