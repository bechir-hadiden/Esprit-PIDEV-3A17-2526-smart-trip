<?php

namespace App\Controller\Client;

use App\Entity\Voyage;
use App\Repository\DestinationRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/voyages', name: 'client_voyage_')]
class ClientVoyageController extends AbstractController
{
    // ── LISTE (lecture seule + recherche + filtre destination) ────
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        VoyageRepository $repo,
        DestinationRepository $destRepo
    ): Response {
        $q             = $request->query->get('q', '');
        $destinationId = $request->query->getInt('destination') ?: null;

        if ($q) {
            $voyages = $repo->search($q);
        } else {
            $voyages = $repo->findDisponibles($destinationId);
        }

        return $this->render('client/voyage/index.html.twig', [
            'voyages'      => $voyages,
            'destinations' => $destRepo->findAll(),
            'query'        => $q,
            'filtreDestId' => $destinationId,
        ]);
    }

    // ── DÉTAIL (lecture seule) ────────────────────────────────────
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Voyage $voyage): Response
    {
        return $this->render('client/voyage/show.html.twig', [
            'voyage' => $voyage,
        ]);
    }
}