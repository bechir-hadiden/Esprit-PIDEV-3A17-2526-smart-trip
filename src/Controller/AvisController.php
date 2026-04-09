<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Service\EmailService;
use App\Service\PredictiveAnalysisService;
use App\Service\GeocodingService;
use App\Service\WeatherService;
use App\Service\TranslationService;
use App\Service\PhotoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis_index')]
    public function index(
        Request $request,
        AvisRepository $avisRepository,
        EntityManagerInterface $entityManager,
        EmailService $emailService,
        PredictiveAnalysisService $predictiveAnalysis,
        GeocodingService $geocoding,
        WeatherService $weather,
        PhotoService $photoService
    ): Response {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setDateAvis(new \DateTime());
            
            // ========== STATUT : EN ATTENTE DE VALIDATION ==========
            $avis->setStatus(Avis::STATUS_PENDING);
            
            // ========== ENRICHISSEMENT AVEC API ==========
            $destination = $avis->getDestination();
            if ($destination) {
                // 1. Géolocalisation
                $location = $geocoding->geocode($destination);
                if ($location) {
                    $avis->setLatitude($location['lat']);
                    $avis->setLongitude($location['lng']);
                    $avis->setDestination($location['formatted_address']);
                    
                    // 2. Météo
                    $weatherData = $weather->getCurrentWeather($location['lat'], $location['lng']);
                    if ($weatherData) {
                        $avis->setWeatherData($weatherData);
                    }
                    
                    // 3. Photos de la destination
                    $cityName = $location['city'] ?? $destination;
                    $photos = $photoService->searchPhotos($cityName, 6);
                    if ($photos['success'] && !empty($photos['photos'])) {
                        $avis->setPhotos($photos['photos']);
                        $avis->setMainPhoto($photos['photos'][0]['url']);
                    }
                }
            }
            
            // ANALYSE PRÉDICTIVE
            $prediction = $predictiveAnalysis->predictSatisfaction($avis);
            $avis->setPredictiveAnalysis($prediction);
            $avis->setSatisfactionScore($prediction['satisfaction_score']);
            
            $entityManager->persist($avis);
            $entityManager->flush();

            // Envoi des emails
            try {
                $emailService->sendConfirmationClient($avis);
                $emailService->sendNotificationAdmin($avis);
                $this->addFlash('success', '✅ Merci pour votre avis ! Il sera visible après validation par notre équipe.');
            } catch (\Exception $e) {
                $this->addFlash('success', '✅ Avis ajouté avec succès !');
            }

            return $this->redirectToRoute('app_avis_index');
        }

        // IMPORTANT: Afficher uniquement les avis APPROUVÉS sur le site client
        $avisList = $avisRepository->findBy(
            ['status' => Avis::STATUS_APPROVED], 
            ['dateAvis' => 'DESC']
        );
        
        $predictiveStats = $predictiveAnalysis->getPredictiveStats();
        
        // Statistiques pour les avis approuvés uniquement
        $stats = [
            'total' => count($avisList),
            'average_note' => $avisRepository->getAverageNote(),
        ];

        return $this->render('avis/index.html.twig', [
            'form' => $form->createView(),
            'avisList' => $avisList,
            'predictiveStats' => $predictiveStats,
            'stats' => $stats
        ]);
    }

    #[Route('/avis/new', name: 'app_avis_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setDateAvis(new \DateTime());
            $avis->setStatus(Avis::STATUS_PENDING); // En attente de validation
            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash('success', '✅ Merci pour votre avis ! Il sera visible après validation.');
            return $this->redirectToRoute('app_avis_index');
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/avis/modifier/{id}', name: 'app_avis_edit', methods: ['GET'])]
    public function getAvis(Avis $avis): JsonResponse
    {
        return $this->json([
            'id' => $avis->getId(),
            'nomClient' => $avis->getNomClient(),
            'email' => $avis->getEmail(),
            'note' => $avis->getNote(),
            'commentaire' => $avis->getCommentaire(),
            'voyageId' => $avis->getVoyageId(),
            'destination' => $avis->getDestination(),
            'latitude' => $avis->getLatitude(),
            'longitude' => $avis->getLongitude(),
            'weatherData' => $avis->getWeatherData(),
            'satisfactionScore' => $avis->getSatisfactionScore(),
            'predictiveAnalysis' => $avis->getPredictiveAnalysis(),
            'photos' => $avis->getPhotos(),
            'mainPhoto' => $avis->getMainPhoto(),
            'status' => $avis->getStatus(),
            'statusText' => $avis->getStatusText()
        ]);
    }

    #[Route('/avis/modifier/{id}', name: 'app_avis_update', methods: ['POST'])]
    public function updateAvis(Request $request, Avis $avis, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $avis->setNomClient($data['nomClient'])
             ->setEmail($data['email'])
             ->setNote($data['note'])
             ->setCommentaire($data['commentaire'])
             ->setVoyageId($data['voyageId'])
             ->setDestination($data['destination'] ?? null);
        
        // Si un admin modifie, on remet en attente (optionnel)
        if ($this->isGranted('ROLE_ADMIN')) {
            $avis->setStatus(Avis::STATUS_PENDING);
        }

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/avis/supprimer/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function deleteAvis(Avis $avis, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/avis/commentaire/{id}', name: 'app_avis_add_comment', methods: ['POST'])]
    public function addComment(Request $request, Avis $avis, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $auteur = $data['auteur'] ?? 'Visiteur';
        $texte = $data['texte'] ?? '';
        
        if (!empty($texte)) {
            $avis->addCommentaire($auteur, $texte);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'commentaires' => $avis->getCommentaires(),
                'total' => $avis->getNombreCommentaires()
            ]);
        }
        
        return $this->json(['success' => false, 'message' => 'Le commentaire ne peut pas être vide'], 400);
    }

    #[Route('/avis/commentaire/{id}/{commentId}', name: 'app_avis_delete_comment', methods: ['POST'])]
    public function deleteComment(Avis $avis, string $commentId, EntityManagerInterface $entityManager): JsonResponse
    {
        $avis->removeCommentaire($commentId);
        $entityManager->flush();
        
        return $this->json(['success' => true]);
    }

    #[Route('/avis/translate/{id}', name: 'app_avis_translate', methods: ['POST'])]
    public function translateComment(int $id, Request $request, AvisRepository $avisRepository, TranslationService $translationService): JsonResponse
    {
        $avis = $avisRepository->find($id);
        if (!$avis) {
            return $this->json(['success' => false, 'error' => 'Avis non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $targetLang = $data['lang'] ?? 'fr';
        
        $commentaire = $avis->getCommentaire();
        $translated = $translationService->translate($commentaire, $targetLang);
        
        if (!$translated || empty($translated['translated'])) {
            return $this->json([
                'success' => false, 
                'error' => 'Traduction non disponible',
                'original' => $commentaire
            ]);
        }
        
        return $this->json([
            'success' => true,
            'original' => $commentaire,
            'translated' => $translated['translated'],
            'source_lang' => $translated['source_lang'] ?? 'fr',
            'target_lang' => $targetLang,
            'is_fallback' => $translated['is_fallback'] ?? false
        ]);
    }

    #[Route('/avis/photos/{id}', name: 'app_avis_photos', methods: ['GET'])]
    public function getPhotos(int $id, AvisRepository $avisRepository): JsonResponse
    {
        $avis = $avisRepository->find($id);
        if (!$avis) {
            return $this->json(['success' => false, 'error' => 'Avis non trouvé'], 404);
        }

        return $this->json([
            'success' => true,
            'photos' => $avis->getPhotos(),
            'mainPhoto' => $avis->getMainPhoto()
        ]);
    }
    
    // ========== NOUVELLE ROUTE POUR LES AVIS PAR STATUT (AJAX) ==========
    #[Route('/avis/filter/{status}', name: 'app_avis_filter', methods: ['GET'])]
    public function filterByStatus(string $status, AvisRepository $avisRepository): JsonResponse
    {
        $validStatuses = [Avis::STATUS_APPROVED, Avis::STATUS_PENDING];
        
        if (!in_array($status, $validStatuses)) {
            $status = Avis::STATUS_APPROVED;
        }
        
        $avis = $avisRepository->findBy(['status' => $status], ['dateAvis' => 'DESC']);
        
        return $this->json([
            'success' => true,
            'count' => count($avis),
            'avis' => array_map(function($a) {
                return [
                    'id' => $a->getId(),
                    'nomClient' => $a->getNomClient(),
                    'note' => $a->getNote(),
                    'commentaire' => $a->getCommentaire(),
                    'dateAvis' => $a->getDateAvis()->format('d/m/Y'),
                    'destination' => $a->getDestination(),
                    'mainPhoto' => $a->getMainPhoto()
                ];
            }, $avis)
        ]);
    }
}