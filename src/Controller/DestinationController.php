<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Service\GeocodingService;
use App\Service\WeatherService;
use App\Service\PhotoService;
use App\Service\FlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DestinationController extends AbstractController
{
    #[Route('/destination/{destination}', name: 'app_destination_show')]
    public function show(
        string $destination,
        GeocodingService $geocoding,
        WeatherService $weather,
        AvisRepository $avisRepository,
        PhotoService $photoService,
        FlightService $flightService
    ): Response {
        // 1. Géolocalisation de la destination
        $location = $geocoding->geocode($destination);
        
        if (!$location) {
            throw $this->createNotFoundException('Destination non trouvée');
        }
        
        // 2. Météo actuelle et prévisions
        $weatherData = $weather->getCurrentWeather($location['lat'], $location['lng']);
        $forecast = $weather->getForecast($location['lat'], $location['lng']);
        
        // 3. Avis pour cette destination
        $avis = $avisRepository->findBy(['destination' => $destination], ['dateAvis' => 'DESC']);
        
        // 4. Photos des voyageurs (depuis les avis)
        $allPhotos = [];
        foreach ($avis as $avi) {
            if ($avi->getPhotos()) {
                $allPhotos = array_merge($allPhotos, $avi->getPhotos());
            }
        }
        // Supprimer les doublons par ID
        $uniquePhotos = [];
        foreach ($allPhotos as $photo) {
            $uniquePhotos[$photo['id']] = $photo;
        }
        $allPhotos = array_values($uniquePhotos);
        
        // 5. Photos Unsplash de la destination
        $destinationPhotos = $photoService->searchPhotos($location['city'] ?? $destination, 20);
        $unsplashPhotos = $destinationPhotos['success'] ? $destinationPhotos['photos'] : [];
        
        // 6. VOLS VERS LA DESTINATION (AVIATIONSTACK)
        $flights = $flightService->getAllFlightsToDestination($location['city'] ?? $destination, 15);
        
        // 7. Statistiques des avis
        $stats = [
            'total' => count($avis),
            'moyenne' => 0,
            'positifs' => 0
        ];
        
        if ($stats['total'] > 0) {
            $notes = array_map(fn($a) => $a->getNote(), $avis);
            $stats['moyenne'] = array_sum($notes) / $stats['total'];
            $stats['positifs'] = count(array_filter($notes, fn($n) => $n >= 4));
        }
        
        // 8. Conseils météo
        $tips = $weatherData ? $weather->getWeatherTips($weatherData['temperature'], $weatherData['description']) : [];
        
        // 9. Code IATA de l'aéroport
        $airportCode = $flightService->getAirportCode($location['city'] ?? $destination);
        
        return $this->render('destination/show.html.twig', [
            'destination' => $destination,
            'location' => $location,
            'weather' => $weatherData,
            'forecast' => $forecast,
            'avis' => $avis,
            'stats' => $stats,
            'tips' => $tips,
            'photos' => $allPhotos,
            'unsplashPhotos' => $unsplashPhotos,
            'flights' => $flights,
            'airportCode' => $airportCode
        ]);
    }
}