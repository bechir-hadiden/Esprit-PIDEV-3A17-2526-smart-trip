<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class ContactService
{
    private const SENDER_EMAIL = 'hello@demomailtrap.com';  // expéditeur (domaine vérifié Mailtrap)
    private const ADMIN_EMAIL  = 'bechirhadidan8@gmail.com'; // destinataire réel (vous)
    private const ADMIN_NAME   = 'SmartTrip Admin';

    public function __construct(
        private MailerInterface $mailer,
    ) {}

    /**
     * Envoie le message du client à l'admin
     * et un email de confirmation au client
     */
    public function envoyerContact(
        string $nomClient,
        string $emailClient,
        string $sujet,
        string $message
    ): void {
        // ── 1. Email à l'admin (vous) ───────────────────────────
        $emailAdmin = (new Email())
            ->from(new Address(self::SENDER_EMAIL, self::ADMIN_NAME))
            ->to(self::ADMIN_EMAIL)                              // ✅ envoyé à bechirhadidan8@gmail.com
            ->replyTo(new Address($emailClient, $nomClient))
            ->subject('📩 Nouveau message : ' . $sujet)
            ->html($this->buildAdminHtml($nomClient, $emailClient, $sujet, $message))
            ->text("De: $nomClient <$emailClient>\nSujet: $sujet\n\n$message");

        $this->mailer->send($emailAdmin);

        // ── 2. Email de confirmation au client ──────────────────
        $emailClient2 = (new Email())
            ->from(new Address(self::SENDER_EMAIL, self::ADMIN_NAME))
            ->to(new Address($emailClient, $nomClient))          // ✅ envoyé au client
            ->subject('✅ Votre message a bien été reçu – SmartTrip')
            ->html($this->buildClientHtml($nomClient, $sujet, $message))
            ->text("Bonjour $nomClient,\n\nNous avons bien reçu votre message concernant : $sujet\n\nNous vous répondrons dans les plus brefs délais.\n\nL'équipe SmartTrip");

        $this->mailer->send($emailClient2);
    }

    // ── Template HTML email admin ──────────────────────────────
    private function buildAdminHtml(string $nom, string $email, string $sujet, string $message): string
    {
        $messageEsc = nl2br(htmlspecialchars($message));
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family:'Segoe UI',sans-serif;background:#f5f0e8;margin:0;padding:2rem;">
          <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
            <div style="background:#0d1b2a;padding:2rem;text-align:center;">
              <h1 style="color:#c9a84c;font-family:Georgia,serif;margin:0;font-size:1.6rem;">✈️ SmartTrip</h1>
              <p style="color:rgba(255,255,255,0.6);margin:.5rem 0 0;font-size:.85rem;">Nouveau message de contact</p>
            </div>
            <div style="padding:2rem;">
              <table style="width:100%;border-collapse:collapse;margin-bottom:1.5rem;">
                <tr>
                  <td style="padding:.6rem 1rem;background:#f5f0e8;border-radius:8px;font-weight:700;color:#0d1b2a;width:30%;font-size:.85rem;">Nom</td>
                  <td style="padding:.6rem 1rem;font-size:.9rem;color:#333;">{$nom}</td>
                </tr>
                <tr>
                  <td style="padding:.6rem 1rem;font-weight:700;color:#0d1b2a;font-size:.85rem;">Email</td>
                  <td style="padding:.6rem 1rem;font-size:.9rem;"><a href="mailto:{$email}" style="color:#c9a84c;">{$email}</a></td>
                </tr>
                <tr>
                  <td style="padding:.6rem 1rem;background:#f5f0e8;border-radius:8px;font-weight:700;color:#0d1b2a;font-size:.85rem;">Sujet</td>
                  <td style="padding:.6rem 1rem;font-size:.9rem;color:#333;">{$sujet}</td>
                </tr>
              </table>
              <div style="background:#f5f0e8;border-left:4px solid #c9a84c;border-radius:8px;padding:1.2rem;">
                <p style="font-weight:700;color:#0d1b2a;margin:0 0 .75rem;font-size:.85rem;text-transform:uppercase;letter-spacing:.5px;">Message</p>
                <p style="color:#555;line-height:1.7;margin:0;font-size:.9rem;">{$messageEsc}</p>
              </div>
              <div style="margin-top:1.5rem;text-align:center;">
                <a href="mailto:{$email}?subject=Re: {$sujet}"
                   style="background:#c9a84c;color:#0d1b2a;padding:.75rem 2rem;border-radius:50px;text-decoration:none;font-weight:700;font-size:.875rem;">
                  ↩ Répondre à {$nom}
                </a>
              </div>
            </div>
            <div style="background:#0d1b2a;padding:1rem;text-align:center;">
              <p style="color:rgba(255,255,255,0.4);font-size:.72rem;margin:0;">SmartTrip — Formulaire de contact</p>
            </div>
          </div>
        </body>
        </html>
        HTML;
    }

    // ── Template HTML email confirmation client ────────────────
    private function buildClientHtml(string $nom, string $sujet, string $message): string
    {
        $messageEsc = nl2br(htmlspecialchars($message));
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family:'Segoe UI',sans-serif;background:#f5f0e8;margin:0;padding:2rem;">
          <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
            <div style="background:#0d1b2a;padding:2rem;text-align:center;">
              <h1 style="color:#c9a84c;font-family:Georgia,serif;margin:0;font-size:1.6rem;">✈️ SmartTrip</h1>
              <p style="color:rgba(255,255,255,0.6);margin:.5rem 0 0;font-size:.85rem;">Confirmation de réception</p>
            </div>
            <div style="padding:2rem;">
              <p style="color:#0d1b2a;font-size:1rem;margin-bottom:1rem;">Bonjour <strong>{$nom}</strong>,</p>
              <p style="color:#555;line-height:1.7;margin-bottom:1.5rem;">
                Nous avons bien reçu votre message et nous vous en remercions.
                Notre équipe vous répondra dans les plus brefs délais.
              </p>
              <div style="background:#f5f0e8;border-left:4px solid #c9a84c;border-radius:8px;padding:1.2rem;margin-bottom:1.5rem;">
                <p style="font-weight:700;color:#0d1b2a;margin:0 0 .5rem;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;">Votre message (sujet : {$sujet})</p>
                <p style="color:#777;line-height:1.7;margin:0;font-size:.85rem;">{$messageEsc}</p>
              </div>
              <p style="color:#555;font-size:.875rem;line-height:1.7;">
                Cordialement,<br>
                <strong style="color:#0d1b2a;">L'équipe SmartTrip</strong>
              </p>
            </div>
            <div style="background:#0d1b2a;padding:1rem;text-align:center;">
              <p style="color:rgba(255,255,255,0.4);font-size:.72rem;margin:0;">© SmartTrip — Ne pas répondre à cet email automatique</p>
            </div>
          </div>
        </body>
        </html>
        HTML;
    }
}