<?php

namespace App\Controller;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RessourceController extends AbstractController
{#[Route('/ressource/pdf/{id}', name: 'app_ressource_pdf')]
    public function generatePdf(int $id, RessourceRepository $ressourceRepository): Response
    {
        // 📌 Récupérer la ressource
        $ressource = $ressourceRepository->find($id);
        
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }
    
        // 📌 Configuration de Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
    
        // 📌 Générer le HTML du PDF
        $html = $this->renderView('GestionStock/Frontoffice/ressource/pdf_ressource.html.twig', [
            'ressource' => $ressource
        ]);
    
        // 📌 Charger et rendre le PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // 📌 Définir le dossier `pdfressource/` pour stocker les fichiers
        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/pdfressource/';
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0777, true); // Créer le dossier s'il n'existe pas
        }
    
        // 📌 Nom du fichier PDF
        $pdfFilename = 'ressource_' . $ressource->getId() . '.pdf';
        $pdfPath = $pdfDirectory . $pdfFilename;
    
        // 📌 Sauvegarder le PDF dans `pdfressource/`
        file_put_contents($pdfPath, $dompdf->output());
    
        // 📌 Retourner le PDF en téléchargement
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $pdfFilename . '"'
            ]
        );
    }
    
    #[Route('/ressource/pdf/list', name: 'app_ressource_pdf_list')]
public function listGeneratedPdfs(): Response
{
    $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/pdfressource/';
    $pdfFiles = [];

    if (file_exists($pdfDirectory)) {
        $pdfFiles = array_diff(scandir($pdfDirectory), ['..', '.']); // Récupérer les fichiers PDF
    }

    return $this->render('GestionStock/Frontoffice/ressource/list-pdfressource.html.twig', [
        'pdfFiles' => $pdfFiles
    ]);
}

        


    #[Route('/ressource', name: 'app_ressource_index')]
    public function index(RessourceRepository $ressourceRepository, MailerInterface $mailer): Response
    {  $resources = $ressourceRepository->findAll();

        foreach ($resources as $resource) {
            $expirationDate = $resource->getDateExpirationRessource();
            if ($expirationDate === null) {
                continue;
            }
            $today = new \DateTime();
            $interval = $today->diff($expirationDate);
            $daysUntilExpiration = $interval->days;

            if ($daysUntilExpiration <= 3) {
                $email = (new Email())
                ->from('your_email@example.com')
                ->to('recipient@example.com')
                ->subject('Resource Expiration Alert')
                ->text(sprintf(
                    'le ressource  "%s" va expirer   %s.',
                    $resource->getNomRessource(),
                    $resource->getDateExpirationRessource()->format('Y-m-d')
                ));
    
                $mailer->send($email); 
            }
        }

        return $this->render('GestionStock/Frontoffice/ressource/index.html.twig', [
        
                'ressources' => $ressourceRepository->findBy(['isshown' => true]),
            ]);
            
        
    }

    #[Route('/ressource/new', name: 'app_ressource_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
           
                       //   ++++++++++++++++++++++++++++++++++++++++++++++++++++++++

                       $depot = $ressource->getDepot();
                       $ressourceQuantity = $ressource->getQuantiteRessource(); // Assuming getQuantiteRessource() exists
                       $depotCapacity = $depot->getCapaciteDepot(); 
               
                       $currentStock = 0;
                       foreach ($depot->getRessource() as $existingRessource) {
                           $currentStock += $existingRessource->getQuantiteRessource();
                       }
               
                        if (($currentStock + $ressourceQuantity) > $depotCapacity) {
                           $this->addFlash('error', 'La quantité de ressource dépasse la capacité du dépôt !');
                       } else {
           
    
            $entityManager->persist($ressource);
            $entityManager->flush();

            return $this->redirectToRoute('app_ressource_index');
        }   }

        return $this->render('GestionStock/Frontoffice/ressource/ajout.html.twig', [
            'title' => 'Ajouter une ressource',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ressource/{id}', name: 'app_ressource_show', requirements: ['id' => '\d+'])]
    public function show(int $id, RessourceRepository $ressourceRepository): Response
    {
        $ressource = $ressourceRepository->find($id);
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        return $this->render('GestionStock/Frontoffice/ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/ressource/edit/{id}', name: 'app_ressource_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, RessourceRepository $ressourceRepository, EntityManagerInterface $entityManager): Response
    {
        $ressource = $ressourceRepository->find($id);
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ressource_index');
        }

        return $this->render('GestionStock/Frontoffice/ressource/modifier.html.twig', [
            'title' => 'Modifier la Ressource',
            'ressource' => $ressource,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ressource/delete/{id}', name: 'app_ressource_delete', requirements: ['id' => '\d+'], methods: ['POST', 'DELETE'])]
    public function delete(int $id, RessourceRepository $ressourceRepository, EntityManagerInterface $entityManager): Response
    {
        $ressource = $ressourceRepository->find($id);
        if (!$ressource) {
            throw $this->createNotFoundException('Ressource non trouvée.');
        }

        $entityManager->remove($ressource);
        $entityManager->flush();

        return $this->redirectToRoute('app_ressource_index');
    }
}
