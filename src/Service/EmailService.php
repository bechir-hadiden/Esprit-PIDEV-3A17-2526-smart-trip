<?php

namespace App\Service;

use App\Entity\Avis;
use App\Entity\Reservation;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    // ========== MÉTHODES POUR LES AVIS ==========

    /**
     * Envoie un email de confirmation au client après avis
     */
    public function sendConfirmationClient(Avis $avis): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to($avis->getEmail())
            ->subject('✅ Confirmation de votre avis - SmartTrip')
            ->html($this->getConfirmationTemplate($avis));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email client: ' . $e->getMessage());
        }
    }

    /**
     * Envoie une notification à l'administrateur pour un nouvel avis
     */
    public function sendNotificationAdmin(Avis $avis): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to('sandesnidhia@gmail.com')
            ->subject('🔔 Nouvel avis reçu - ' . $avis->getNomClient())
            ->html($this->getAdminNotificationTemplate($avis));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email admin: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email au client quand son avis est approuvé
     */
    public function sendAvisApproved(Avis $avis): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to($avis->getEmail())
            ->subject('✅ Votre avis a été publié - SmartTrip')
            ->html($this->getAvisApprovedTemplate($avis));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email avis approuvé: ' . $e->getMessage());
        }
    }

    // ========== MÉTHODES POUR LES RÉSERVATIONS ==========

    /**
     * Envoie un email de confirmation de réservation au client
     */
    public function sendReservationConfirmation(Reservation $reservation): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to($reservation->getEmail())
            ->subject('✈️ Confirmation de votre réservation - SmartTrip')
            ->html($this->getReservationConfirmationTemplate($reservation));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email confirmation réservation: ' . $e->getMessage());
        }
    }

    /**
     * Notifie l'administrateur d'une nouvelle réservation
     */
    public function notifyNewReservation(Reservation $reservation): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to('sandesnidhia@gmail.com')
            ->subject('🆕 Nouvelle réservation - ' . $reservation->getReservationNumber())
            ->html($this->getNewReservationNotificationTemplate($reservation));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi notification admin réservation: ' . $e->getMessage());
        }
    }

    /**
     * Envoie une notification au client quand sa réservation est confirmée
     */
    public function notifyReservationConfirmed(Reservation $reservation): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to($reservation->getEmail())
            ->subject('✅ Votre réservation a été confirmée - SmartTrip')
            ->html($this->getReservationConfirmedTemplate($reservation));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi notification confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Envoie la carte d'embarquement par email
     */
    public function sendBoardingPassEmail(Reservation $reservation, string $boardingPassHtml): void
    {
        $email = (new Email())
            ->from(new Address('sandesnidhia@gmail.com', 'SmartTrip Voyages'))
            ->to($reservation->getEmail())
            ->subject('🎫 Votre carte d\'embarquement - Vol ' . $reservation->getFlightNumber())
            ->html($boardingPassHtml);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email carte d\'embarquement: ' . $e->getMessage());
        }
    }

    // ========== TEMPLATES POUR LES AVIS ==========

    /**
     * Template HTML pour la confirmation client
     */
    private function getConfirmationTemplate(Avis $avis): string
    {
        $etoiles = str_repeat('⭐', $avis->getNote()) . str_repeat('☆', 5 - $avis->getNote());
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .avis-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                .footer { text-align: center; margin-top: 30px; color: #999; font-size: 12px; }
                .badge { display: inline-block; background: #667eea; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; }
                .stars { font-size: 24px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✨ Merci pour votre avis !</h1>
                    <p>Votre expérience nous aide à améliorer nos services</p>
                </div>
                
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($avis->getNomClient()) . '</strong>,</p>
                    
                    <p>Nous vous remercions d\'avoir pris le temps de partager votre expérience sur SmartTrip.</p>
                    
                    <div class="avis-info">
                        <h3 style="margin-top: 0;">Récapitulatif de votre avis</h3>
                        <p><strong>Voyage #' . $avis->getVoyageId() . '</strong></p>
                        <div class="stars">' . $etoiles . '</div>
                        <p><strong>Commentaire :</strong><br>' . nl2br(htmlspecialchars($avis->getCommentaire())) . '</p>
                        <p><span class="badge">Note : ' . $avis->getNote() . '/5</span></p>
                    </div>
                    
                    <p>Votre avis sera visible après validation par notre équipe.</p>
                    
                    <p>À très bientôt sur SmartTrip !</p>
                    
                    <p style="margin-top: 30px;">L\'équipe SmartTrip</p>
                </div>
                
                <div class="footer">
                    <p>© ' . date('Y') . ' SmartTrip Voyages. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Template HTML pour la notification admin
     */
    private function getAdminNotificationTemplate(Avis $avis): string
    {
        $etoiles = str_repeat('⭐', $avis->getNote()) . str_repeat('☆', 5 - $avis->getNote());
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .avis-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff6b6b; }
                .client-info { background: #f0f0f0; padding: 15px; border-radius: 8px; margin: 10px 0; }
                .label { font-weight: bold; color: #666; }
                .stars { font-size: 20px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📬 Nouvel avis reçu !</h1>
                    <p>Un client a partagé son expérience</p>
                </div>
                
                <div class="content">
                    <h3>Détails du client</h3>
                    <div class="client-info">
                        <p><span class="label">Nom :</span> ' . htmlspecialchars($avis->getNomClient()) . '</p>
                        <p><span class="label">Email :</span> ' . htmlspecialchars($avis->getEmail()) . '</p>
                        <p><span class="label">Voyage # :</span> ' . $avis->getVoyageId() . '</p>
                    </div>
                    
                    <h3>Détails de l\'avis</h3>
                    <div class="avis-info">
                        <p><span class="label">Date :</span> ' . $avis->getDateAvis()->format('d/m/Y H:i') . '</p>
                        <div class="stars">' . $etoiles . '</div>
                        <p><span class="label">Note :</span> ' . $avis->getNote() . '/5</p>
                        <p><span class="label">Commentaire :</span></p>
                        <p style="background: #f5f5f5; padding: 15px; border-radius: 5px;">' . nl2br(htmlspecialchars($avis->getCommentaire())) . '</p>
                    </div>
                    
                    <p><a href="http://127.0.0.1:8000/admin/avis" style="display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">📋 Gérer les avis</a></p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Template HTML pour l'approbation d'avis
     */
    private function getAvisApprovedTemplate(Avis $avis): string
    {
        $etoiles = str_repeat('⭐', $avis->getNote()) . str_repeat('☆', 5 - $avis->getNote());
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .avis-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
                .btn { display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
                .stars { font-size: 24px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Votre avis est en ligne !</h1>
                    <p>Merci d\'avoir partagé votre expérience</p>
                </div>
                
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($avis->getNomClient()) . '</strong>,</p>
                    
                    <p>Nous avons le plaisir de vous informer que votre avis a été approuvé et est désormais visible sur notre site.</p>
                    
                    <div class="avis-info">
                        <h3 style="margin-top: 0;">Récapitulatif de votre avis</h3>
                        <div class="stars">' . $etoiles . '</div>
                        <p><strong>Votre commentaire :</strong><br>' . nl2br(htmlspecialchars($avis->getCommentaire())) . '</p>
                    </div>
                    
                    <p>Votre avis aide d\'autres voyageurs à faire le bon choix. Merci pour votre contribution !</p>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <a href="http://127.0.0.1:8000/avis" class="btn">🔍 Voir tous les avis</a>
                    </div>
                    
                    <p style="margin-top: 30px;">À très bientôt sur SmartTrip !</p>
                    
                    <p>L\'équipe SmartTrip</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    // ========== TEMPLATES POUR LES RÉSERVATIONS ==========

    /**
     * Template HTML pour la confirmation de réservation
     */
    private function getReservationConfirmationTemplate(Reservation $reservation): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
                .flight-details { background: #f0f0f0; padding: 15px; border-radius: 8px; margin: 10px 0; }
                .label { font-weight: bold; color: #666; }
                .status-pending { display: inline-block; background: #ffc107; color: #333; padding: 5px 15px; border-radius: 20px; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✈️ Réservation enregistrée !</h1>
                    <p>Nous avons bien reçu votre demande</p>
                </div>
                
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($reservation->getNomClient()) . '</strong>,</p>
                    
                    <p>Votre réservation a bien été enregistrée et est en cours de traitement.</p>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0;">📋 Détails de votre réservation</h3>
                        <p><strong>Numéro de réservation :</strong> ' . $reservation->getReservationNumber() . '</p>
                        <p><strong>Statut :</strong> <span class="status-pending">En attente de confirmation</span></p>
                    </div>
                    
                    <div class="flight-details">
                        <h4>🛫 Détails du vol</h4>
                        <p><span class="label">Compagnie :</span> ' . htmlspecialchars($reservation->getAirline()) . '</p>
                        <p><span class="label">Vol :</span> ' . htmlspecialchars($reservation->getFlightNumber()) . '</p>
                        <p><span class="label">Destination :</span> ' . htmlspecialchars($reservation->getDestination()) . '</p>
                        <p><span class="label">Départ :</span> ' . $reservation->getDepartureAirport() . ' le ' . $reservation->getDepartureTime()->format('d/m/Y à H:i') . '</p>
                        <p><span class="label">Arrivée :</span> ' . $reservation->getArrivalAirport() . ' le ' . $reservation->getArrivalTime()->format('d/m/Y à H:i') . '</p>
                        <p><span class="label">Passagers :</span> ' . $reservation->getNumberOfPassengers() . '</p>
                        <p><span class="label">Prix total :</span> <strong>' . number_format($reservation->getTotalPrice(), 2) . ' €</strong></p>
                    </div>
                    
                    <p>Vous recevrez un email de confirmation dès que votre réservation sera validée par nos services.</p>
                    
                    <p>À très bientôt sur SmartTrip !</p>
                    
                    <p style="margin-top: 30px;">L\'équipe SmartTrip</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Template HTML pour la notification admin de nouvelle réservation
     */
    private function getNewReservationNotificationTemplate(Reservation $reservation): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff6b6b; }
                .btn { display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🆕 Nouvelle réservation !</h1>
                    <p>Une nouvelle réservation nécessite votre attention</p>
                </div>
                
                <div class="content">
                    <div class="info-box">
                        <h3 style="margin-top: 0;">Client</h3>
                        <p><strong>Nom :</strong> ' . htmlspecialchars($reservation->getNomClient()) . '</p>
                        <p><strong>Email :</strong> ' . htmlspecialchars($reservation->getEmail()) . '</p>
                        <p><strong>Téléphone :</strong> ' . ($reservation->getTelephone() ?? 'Non renseigné') . '</p>
                    </div>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0;">Vol</h3>
                        <p><strong>Vol :</strong> ' . htmlspecialchars($reservation->getFlightNumber()) . '</p>
                        <p><strong>Destination :</strong> ' . htmlspecialchars($reservation->getDestination()) . '</p>
                        <p><strong>Départ :</strong> ' . $reservation->getDepartureTime()->format('d/m/Y H:i') . '</p>
                        <p><strong>Prix :</strong> ' . number_format($reservation->getTotalPrice(), 2) . ' €</p>
                    </div>
                    
                    <a href="http://127.0.0.1:8000/admin/reservations" class="btn">📋 Gérer les réservations</a>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Template HTML pour la confirmation de réservation (quand admin confirme)
     */
    private function getReservationConfirmedTemplate(Reservation $reservation): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
                .btn { display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
                .status-confirmed { display: inline-block; background: #28a745; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Réservation confirmée !</h1>
                    <p>Votre voyage est maintenant confirmé</p>
                </div>
                
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($reservation->getNomClient()) . '</strong>,</p>
                    
                    <p>Bonne nouvelle ! Votre réservation a été confirmée par nos services.</p>
                    
                    <div class="info-box">
                        <h3 style="margin-top: 0;">📋 Confirmation</h3>
                        <p><strong>Numéro de réservation :</strong> ' . $reservation->getReservationNumber() . '</p>
                        <p><strong>Statut :</strong> <span class="status-confirmed">Confirmé</span></p>
                        <p><strong>Vol :</strong> ' . htmlspecialchars($reservation->getFlightNumber()) . '</p>
                        <p><strong>Date de départ :</strong> ' . $reservation->getDepartureTime()->format('d/m/Y H:i') . '</p>
                    </div>
                    
                    <p>Vous pouvez maintenant accéder à votre carte d\'embarquement en cliquant sur le lien ci-dessous :</p>
                    
                    <a href="http://127.0.0.1:8000/reservation/' . $reservation->getId() . '/boarding-pass" class="btn">🎫 Voir ma carte d\'embarquement</a>
                    
                    <p style="margin-top: 30px;">Bon voyage !</p>
                    
                    <p>L\'équipe SmartTrip</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
}