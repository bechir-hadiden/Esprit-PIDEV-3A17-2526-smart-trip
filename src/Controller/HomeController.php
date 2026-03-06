<?php

namespace App\Controller;

use App\Repository\DestinationRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        DestinationRepository $destinationRepo,
        VoyageRepository $voyageRepo
    ): Response {
        return $this->render('home/index.html.twig', [
            // Destinations pour le grid et la barre de recherche (max 5)
            'destinations' => $destinationRepo->findBy([], ['order' => 'ASC'], 5),

            // Voyages pour la section packages (max 3)
            'voyages'      => $voyageRepo->findPopulaires(3),

            // Stats dynamiques depuis BDD
            'stats' => [
                'voyages'      => $voyageRepo->count([]),
                'destinations' => $destinationRepo->count([]),
            ],
        ]);
    }
}