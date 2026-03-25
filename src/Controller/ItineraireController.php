<?php
namespace App\Controller;

use App\Repository\VoyageRepository;
use App\Service\N8nItineraireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItineraireController extends AbstractController
{
    #[Route('/itineraire/{id}', name: 'itineraire_generer', methods: ['GET'])]
    public function generer(
        int                  $id,
        VoyageRepository     $voyageRepo,
        N8nItineraireService $n8n
    ): Response {
        $voyage = $voyageRepo->find($id);
        if (!$voyage) {
            throw $this->createNotFoundException('Voyage introuvable');
        }

        $destination = $voyage->getDestinationRel();
        if (!$destination) {
            $this->addFlash('warning', 'Ce voyage n\'a pas de destination liée.');
            return $this->redirectToRoute('app_voyage_index');
        }

        $itineraire = $n8n->genererItineraire($voyage, $destination);

        return $this->render('itineraire/resultat.html.twig', [
            'voyage'      => $voyage,
            'destination' => $destination,
            'itineraire'  => $itineraire,
        ]);
    }
}