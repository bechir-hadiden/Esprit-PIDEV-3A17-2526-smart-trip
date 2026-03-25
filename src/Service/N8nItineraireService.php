<?php
namespace App\Service;

use App\Entity\Destination;
use App\Entity\Voyage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class N8nItineraireService
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface     $logger,
        private string              $webhookUrl
    ) {}

  public function genererItineraire(Voyage $voyage, Destination $destination): array
{
    $payload = [
        'destination_nom'         => $destination->getNom(),
        'destination_pays'        => $destination->getPays(),
        'destination_description' => $destination->getDescription(),
        'destination_iata'        => $destination->getCodeIata(),
        'pays_depart'             => $voyage->getPaysDepart() ?? 'Tunisie',
        'duree_jours'             => $voyage->getDureeJours(),
        'date_debut'              => $voyage->getDateDebut()?->format('Y-m-d'),
        'date_fin'                => $voyage->getDateFin()?->format('Y-m-d'),
        'budget'                  => $voyage->getPrix(),
        'description'             => $voyage->getDescription(),
    ];

    try {
        $response = $this->client->request('POST', $this->webhookUrl, [
            'json'    => $payload,
            'timeout' => 30,
        ]);

        // Récupère le contenu brut
$content = $response->getContent(false);
$statusCode = $response->getStatusCode();dump([
    'status' => $statusCode,
    'content' => $content,
    'headers' => $response->getHeaders(false),
]);        die();
        // Décode la réponse
        $data = json_decode($content, true);

        // Si null ou pas un array
        if (!is_array($data)) {
            return ['erreur' => 'Réponse invalide : ' . $content];
        }

        // Si tableau indexé [0] => {...}
        if (isset($data[0])) {
            $data = $data[0];
        }

        // Si itineraire est une string JSON imbriquée
        if (isset($data['itineraire']) && is_string($data['itineraire'])) {
            $parsed = json_decode(
                preg_replace('/```json\n?|```/', '', $data['itineraire']),
                true
            );
            if ($parsed !== null) {
                $data['itineraire'] = $parsed;
            }
        }

        $this->logger->info('n8n itinéraire reçu', [
            'destination' => $destination->getNom(),
        ]);

        return $data['itineraire'] ?? $data['jours'] ?? $data;

    } catch (\Throwable $e) {
        $this->logger->error('Erreur n8n', ['message' => $e->getMessage()]);
        return ['erreur' => 'Impossible de générer l\'itinéraire : ' . $e->getMessage()];
    }
}
}