<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\Table(name: 'paiements')]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?float $montant = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $statutPaiement = 'En attente';

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $methodePaiement = 'Carte Bancaire';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\ManyToOne(targetEntity: Reservation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    public function __construct()
    {
        $this->datePaiement = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getDatePaiement(): ?\DateTimeInterface { return $this->datePaiement; }
    public function setDatePaiement(\DateTimeInterface $datePaiement): self { $this->datePaiement = $datePaiement; return $this; }

    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }

    public function getStatutPaiement(): ?string { return $this->statutPaiement; }
    public function setStatutPaiement(string $statutPaiement): self { $this->statutPaiement = $statutPaiement; return $this; }

    public function getMethodePaiement(): ?string { return $this->methodePaiement; }
    public function setMethodePaiement(string $methodePaiement): self { $this->methodePaiement = $methodePaiement; return $this; }

    public function getStripeSessionId(): ?string { return $this->stripeSessionId; }
    public function setStripeSessionId(?string $stripeSessionId): self { $this->stripeSessionId = $stripeSessionId; return $this; }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(?Reservation $reservation): self { $this->reservation = $reservation; return $this; }
}
