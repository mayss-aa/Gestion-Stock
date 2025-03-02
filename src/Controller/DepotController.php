<?php
namespace App\Controller;
use App\Repository\DepotRepository;
use App\Repository\RessourceRepository;
use App\Repository\StatistiqueRessoureceRepository;
use App\Form\DepotType;
use Endroid\QrCode\Builder\Builder;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Endroid\QrCode\Encoding\Encoding;
use App\Entity\Depot;
use Dompdf\Options;
use Dompdf\Dompdf;
use App\Entity\StatistiqueDepot;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Repository\StatistiqueDepotRepository;


final class DepotController extends AbstractController
{ 





















    #[Route('/depot', name: 'app_depot_index')]
    public function index(DepotRepository $depotRepository): Response
    {

        $depots = $depotRepository->findAll();
        $depotData = [];

        foreach ($depots as $depot) {
            $totalRessource = 0;
            foreach ($depot->getRessources() as $ressource) {
                $totalRessource += $ressource->getQuantiteRessource(); // Assuming getQuantite() exists
            }

            $depotData[] = [
                'depot' => $depot,
                'totalRessource' => $totalRessource
            ];
        }



        // On récupère tous les dépôts de la base de données et on les passe à 'index.html.twig'
        return $this->render('GestionStock/Frontoffice/depot/index.html.twig', [
            'depotData' => $depotData,
        ]);
    }










    private EntityManagerInterface $entityManager;

    // Injection de EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function getCapaciteEnM3(): float
    {
        return $this->convertToM3($this->capacite_depot, $this->unite_cap_depot);
    }
    
    
    #[Route('/depot/prevision', name: 'app_depot_prevision_no_nom')]
    #[Route('/depot/prevision/{id}', name: 'app_depot_prevision', methods: ['GET'])]
    public function prevoirDepot(
        DepotRepository $depotRepository,
        StatistiqueDepotRepository $statistiqueDepotRepository,
        EntityManagerInterface $entityManager, // 📌 Ajout de l'EntityManager
        int $id = null
    ): Response {
        if ($id === null) {
            return $this->render('GestionStock/Frontoffice/depot/prevision_simple.html.twig', [
                'depots' => $depotRepository->findAll(),
                'depot' => null,
                'prevision' => null,
                'dates' => [],
                'tauxRemplissage' => []
            ]);
        }
    
        $depot = $depotRepository->find($id);
    
        if (!$depot) {
            return $this->render('GestionStock/Frontoffice/depot/prevision_simple.html.twig', [
                'depots' => $depotRepository->findAll(),
                'depot' => null,
                'prevision' => "⚠️ Dépôt introuvable",
                'dates' => [],
                'tauxRemplissage' => []
            ]);
        }
    
        // ✅ Générer une nouvelle statistique automatique
        $stat = new StatistiqueDepot();
        $stat->setDepot($depot);
        $stat->setDate(new \DateTime()); // Date actuelle
        $stat->setTauxRemplissage(mt_rand(10, 100)); // Valeur aléatoire entre 10% et 100%
    
        $entityManager->persist($stat);
        $entityManager->flush();
    
        // ✅ Récupération des valeurs du dépôt
        $depot->calculateTauxAugmentation();
        $utilisationActuelle = $depot->getUtilisationEnM3();
        $tauxAugmentation = $depot->getTauxAugmentationEnM3();
        $capaciteDepot = $depot->getCapaciteEnM3();
    
        // ✅ Récupération des statistiques du dépôt
        $stats = $statistiqueDepotRepository->findBy(['depot' => $depot], ['date' => 'ASC']);
    
        // ✅ Transformation des données pour l'affichage dans Chart.js
        $dates = [];
        $tauxRemplissage = [];
    
        foreach ($stats as $stat) {
            $dates[] = $stat->getDate()->format('Y-m-d');  // Format ISO pour Chart.js
            $tauxRemplissage[] = $stat->getTauxRemplissage();
        }
    
        // ✅ Vérification du script Python
        $scriptPath = $this->getParameter('kernel.project_dir') . '/public/python/predict.py';
    
        if (!file_exists($scriptPath)) {
            $prevision = "❌ Erreur : Script Python introuvable.";
        } else {
            // ✅ Exécution du script Python
            $pythonExecutable = 'C:\\Users\\mayss\\Downloads\\pidev-finalllllll\\pidev-finalllllll\\.venv\\Scripts\\python.exe';
            $process = new Process([
                $pythonExecutable,
                $scriptPath,
                (string) $utilisationActuelle,
                (string) $tauxAugmentation,
                (string) $capaciteDepot
            ]);
    
            try {
                $process->mustRun();
                $output = trim($process->getOutput());
    
                // Vérifier si le résultat est numérique
                $prevision = is_numeric($output) ? number_format((float)$output, 2) : "❌ Erreur : Valeur invalide";
    
            } catch (ProcessFailedException $exception) {
                $prevision = "❌ Erreur d'exécution du script Python.";
            }
        }
    
        // ✅ Retourner les valeurs au template
        return $this->render('GestionStock/Frontoffice/depot/prevision_simple.html.twig', [
            'depot' => $depot,
            'prevision' => $prevision,
            'depots' => $depotRepository->findAll(),
            'capacite_depot' => isset($depot) ? $depot->getCapaciteEnM3() : null,
            'utilisation_actuelle' => $utilisationActuelle,
            'taux_augmentation' => $tauxAugmentation,
            'dates' => $dates,
            'tauxRemplissage' => $tauxRemplissage,
        ]);
    }
    
    // Route pour afficher la liste des dépôts
    #[Route('/depot/list', name: 'app_depot_list')]
    public function listDepot(DepotRepository $depotRepository,  MailerInterface $mailer): Response
    {
        // Récupérer tous les dépôts
        $depots = $depotRepository->findAll();
        foreach ($depots as $depot) {
            $depot->calculateTauxAugmentation();  //  Met à jour le taux d'augmentation avant affichage
        }
        // Mettre à jour l'utilisation actuelle et le taux d'augmentation pour chaque dépôt
        foreach ($depots as $depot) {
            $this->updateDepotUsage($depot);  // Mise à jour automatique
        }
        $depotData = [];
        foreach ($depots as $depot) {
            $totalRessource = 0;
            foreach ($depot->getRessources()as $ressource) {
                $totalRessource += $ressource->getQuantiteRessource(); // Assuming getQuantite() exists
            }

            if ($totalRessource >= $depot->getLimitedby()) {
                $email = (new Email())
                    ->from('mayssa@esprit.tn')
                    ->to('recipient@esprit.com') // Change to actual recipient email
                    ->subject('Your depot is about to be full')
                    ->text("Warning: The depot '{$depot->getNomDepot()}' is reaching its limit. 
                            Current stock: $totalRessource / {$depot->getLimitedby()}.");

                $mailer->send($email);
            }
            $depotData[] = [
                'id' => $depot->getId(),
                'nomDepot' => $depot->getNomDepot(),
                'localisationDepot' => $depot->getLocalisationDepot(),
                'capaciteDepot' => $depot->getCapaciteDepot(),
                'uniteCapDepot' => $depot->getUniteCapDepot(),
                'typeStockageDepot' => $depot->getTypeStockageDepot(),
                'statutDepot' => $depot->getStatutDepot(),
                'limitedby' => $depot->getLimitedby(),
                'totalRessource' => $totalRessource,
                'utilisationActuelle' => $depot->getUtilisationActuelle(),  // ✅ Ajouté
                'tauxAugmentation' => $depot->getTauxAugmentation(),  // ✅ Ajouté
                'ressources' => $depot->getRessources(), // Send resources for loop in Twig
                'ishown' => $depot->getIsshown(), // Send resources for loop in Twig
            ];
        }


        // Rendre la vue
        return $this->render('GestionStock/Frontoffice/depot/index.html.twig', [
            'depotData' => $depotData,
        ]);
    }

    // Méthode pour calculer et mettre à jour l'utilisation actuelle et le taux d'augmentation
 

    
    private function updateDepotUsage(Depot $depot): void
    {
        // 📌 Calculer l'utilisation actuelle (convertie en m³)
        $utilisationActuelle = 0;
        foreach ($depot->getRessources() as $ressource) {
            $utilisationActuelle += $this->convertToM3($ressource->getQuantiteRessource(), $ressource->getUniteMesure());
        }
    
        // 📌 Mettre à jour l'utilisation actuelle
        $depot->setUtilisationActuelle($utilisationActuelle);
    
        // 📌 Calculer et mettre à jour le taux d'augmentation
        $tauxAugmentation = $depot->calculateTauxAugmentation();

        $depot->setTauxAugmentation($tauxAugmentation);
    
        // 📌 Sauvegarder les mises à jour en base de données
        $this->entityManager->persist($depot);
        $this->entityManager->flush();
    }
  // 🛠 Fonction pour calculer le taux d'augmentation en m³/jour
 

    
// Fonction de conversion de L ou kg en m³
public function convertToM3(float $quantite, string $unite): float
{
    // Nettoyer l'unité pour éviter les erreurs dues aux espaces invisibles
    $unite = trim($unite);

    switch ($unite) {
        case 'kg':
            return $quantite * 0.001; // 1 kg = 0.001 m³ (exemple générique)
        case 'L':
            return $quantite * 0.001; // 1 L = 0.001 m³
        case 'm3':
        case 'm³':  // Ajout d’une variante pour éviter les erreurs
            return floatval($quantite); // S’assurer que la valeur est bien en float
        default:
            return 0; // Retourner 0 si l’unité est inconnue
    }
}


    

    // Route pour ajouter un dépôt
    #[Route('/depot/add', name: 'app_depot_add')]
    public function addDepot(): Response
    {
        $depot = new Depot();
        $depot->setNomDepot('Dépôt Central')
            ->setLocalisationDepot('Tunis')
            ->setCapaciteDepot(500)
            ->setUniteCapDepot('m³')
            ->setTypeStockageDepot('Réfrigéré')
            ->setStatutDepot('Actif');
          
            $entityManager->persist($depot);
            $entityManager->flush();
            $this->updateDepotUsage($depot);

        return new Response('Dépôt ajouté avec succès.');
    }

    // Route pour afficher et gérer le formulaire de création d'un dépôt
    #[Route('/depot/new', name: 'app_depot_new')]
    public function newDepot(Request $request): Response
    {
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            entityManager->persist($depot);
            entityManager->flush();

            // Mettre à jour l'utilisation actuelle et le taux d'augmentation après la sauvegarde
            $this->updateDepotUsage($depot);

            return $this->redirectToRoute('app_depot_list');
        }

        return $this->render('GestionStock/Frontoffice/depot/ajout.html.twig', [
            'title' => 'Ajouter un Dépôt',
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher et gérer le formulaire de modification d'un dépôt
    #[Route('/depot/edit/{id}', name: 'app_depot_edit', requirements: ['id' => '\d+'])]
    public function editDepot(int $id, DepotRepository $depotRepository, Request $request): Response
    {
        $depot = $depotRepository->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé.');
        }

        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            // Mettre à jour l'utilisation actuelle et le taux d'augmentation après modification
            $this->updateDepotUsage($depot);

            return $this->redirectToRoute('app_depot_list');
        }

        return $this->render('GestionStock/Frontoffice/depot/modifier.html.twig', [
            'title' => 'Modifier le Dépôt',
            'form' => $form->createView(),
            'depot' => $depot,
        ]);
    }

    // Route pour supprimer un dépôt
    #[Route('/depot/delete/{id}', name: 'app_depot_delete', requirements: ['id' => '\d+'], methods: ['POST', 'DELETE'])]
    public function deleteDepot(int $id, DepotRepository $depotRepository): Response
    {
        $depot = $depotRepository->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé.');
        }

        $this->entityManager->remove($depot);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_depot_list');
    }


    #[Route('/depot/{id}', name: 'app_depot_show', requirements: ['id' => '\d+'])]
    public function showDepot(int $id, DepotRepository $depotRepository): Response
    {
        // Trouver le dépôt par son ID
        $depot = $depotRepository->find($id);
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé.');
        }
        $totale = 0;
        foreach ($depot->getRessources() as $ressource) {

            $totale++;
        }
        $data = 'Nom depot : ' . $depot->getNomDepot()  . '  || Nombre des ressources : ' . $totale;

        $result = Builder::create()
            ->writer(new \Endroid\QrCode\Writer\SvgWriter())
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->build();
        $dataUri = $result->getDataUri();


        // Retourner la vue 'show.html.twig' avec les détails du dépôt
        return $this->render('GestionStock/Frontoffice/depot/show.html.twig', [
            'depot' => $depot,
            'QR' => $dataUri
        ]);
    }

    #[Route('/depot/search', name: 'api_search', methods: ['GET'])]
    public function search(Request $request, DepotRepository $repository): JsonResponse
    {
        $query = $request->query->get('search', '');
    
        if (!$query) {
            return $this->json([]);
        }
    
        // Search for matching Depot entities
        $results = $repository->createQueryBuilder('d')
            ->where('d.nom_depot LIKE :search OR d.localisation_depot LIKE :search')
            ->andWhere('d.isshown = :status') // Ensuring only visible depots are shown
            ->setParameter('search', "%$query%")
            ->setParameter('status', true)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    
        // Convert entities to an array
        $data = array_map(fn($depot) => [
            'id' => $depot->getId(),
            'nomDepot' => $depot->getNomDepot(),
            'localisationDepot' => $depot->getLocalisationDepot(),
            'capaciteDepot' => $depot->getCapaciteDepot(),
            'uniteCapDepot' => $depot->getUniteCapDepot(),
            'typeStockageDepot' => $depot->getTypeStockageDepot(),
            'statutDepot' => $depot->getStatutDepot(),
            'isshown' => $depot->getIsshown(),
        ], $results);
    
        return $this->json($data);
    }





    


    #[Route('/depot/isshown/{id}', name: 'app_depot_shown', methods: ['POST'])]
    public function toggleVisibility(int $id, DepotRepository $depotRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Trouver le dépôt par son ID
        $depot = $depotRepository->find($id);
    
        if (!$depot) {
            return new JsonResponse(['error' => 'Dépôt introuvable'], Response::HTTP_NOT_FOUND);
        }
    
        // Inverser l'état d'affichage
        $depot->setIsshown(!$depot->getIsshown());
        $isShown = $depot->getIsshown();  // ➜ Récupérer la nouvelle valeur (true/false)
    
        // Récupérer les IDs des ressources liées à ce dépôt
        $ressourceIds = [];
        foreach ($depot->getRessources() as $ressource) {
            // ➜ Ajouter cette ligne pour que la ressource ait le même statut que le dépôt
            $ressource->setIsshown($isShown);
    
            $ressourceIds[] = $ressource->getId();
    
            // ➜ Persister aussi la ressource pour enregistrer la mise à jour en base
            $entityManager->persist($ressource);
        }
    
        // Sauvegarde en base de données
        $entityManager->persist($depot);
        $entityManager->flush();
    
        return new JsonResponse([
            'id' => $depot->getId(),
            'isShown' => $depot->getIsshown(),
            'ressourceIds' => $ressourceIds // Retourner les ressources liées
        ]);
    }

    #[Route('/depot/updates', name: 'app_depot_updates', methods: ['GET'])]
public function getDepotUpdates(DepotRepository $depotRepository): JsonResponse
{
    $depots = $depotRepository->findAll();
    $data = [];

    foreach ($depots as $depot) {
        $ressourceIds = [];
        foreach ($depot->getRessources() as $ressource) {
            $ressourceIds[] = $ressource->getId();
        }

        $data[] = [
            'id' => $depot->getId(),
            'isShown' => $depot->getIsshown(),
            'ressourceIds' => $ressourceIds
        ];
    }

    return new JsonResponse($data);
}

}