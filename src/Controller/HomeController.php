<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\DestinationRepository;
use App\Repository\VoyageRepository;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        DestinationRepository $destRepo,
        VoyageRepository $voyageRepo,
        ContactService $contactService
    ): Response {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Construit le message complet avec tous les champs du formulaire
            $message = "Prénom : {$data['prenom']}\n";
            $message .= "Nom : {$data['nom']}\n";
            $message .= "Téléphone : " . ($data['telephone'] ?? '—') . "\n";
            $message .= "Destination souhaitée : " . ($data['destinationSouhaitee'] ?? '—') . "\n";
            $message .= "Budget estimé : " . ($data['budget'] ?? '—') . "\n\n";
            $message .= "Message :\n" . $data['message'];

            try {
                $contactService->envoyerContact(
                    $data['prenom'] . ' ' . $data['nom'],
                    $data['email'],
                    'Demande de devis – SmartTrip',
                    $message
                );
                $this->addFlash('contact_success', '✅ Votre demande a été envoyée ! Un conseiller vous contactera sous 24h.');
            } catch (\Exception $e) {
                $this->addFlash('contact_error', '❌ Erreur lors de l\'envoi. Veuillez réessayer.');
            }

            // ✅ CORRIGÉ : nom de route correct + fragment pour ancre #contact
            return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
        }

        return $this->render('home/index.html.twig', [
            // ✅ CORRIGÉ : 'order' remplacé par 'id' (champ valide)
            'destinations' => $destRepo->findBy([], ['id' => 'ASC'], 6),
            'voyages'      => $voyageRepo->findBy([], ['id' => 'DESC'], 4),
            'contactForm'  => $form->createView(),
        ]);
    }
}