<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request, ContactService $contactService): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $contactService->envoyerContact(
                    $data['nom'],
                    $data['email'],
                    $data['sujet'],
                    $data['message']
                );

                $this->addFlash('success', '✅ Votre message a été envoyé ! Vous recevrez une confirmation par email.');
                return $this->redirectToRoute('contact');

            } catch (\Exception $e) {
                $this->addFlash('error', '❌ Erreur lors de l\'envoi. Veuillez réessayer.');
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}