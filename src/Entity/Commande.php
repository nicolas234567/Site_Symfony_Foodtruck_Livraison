<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_PRETE      = 'prete';
    public const STATUT_LIVREE     = 'livree';

    public const STATUTS = [
        'En attente' => self::STATUT_EN_ATTENTE,
        'Prête'      => self::STATUT_PRETE,
        'Livrée'     => self::STATUT_LIVREE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 20)]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignesCommande;

    public function __construct()
    {
        $this->lignesCommande = new ArrayCollection();
        $this->date = new \DateTime();
        $this->statut = self::STATUT_EN_ATTENTE;
    }

    public function getId(): ?int { return $this->id; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): static { $this->date = $date; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getStatutLabel(): string
    {
        return array_search($this->statut, self::STATUTS) ?: $this->statut;
    }

    public function getClient(): ?User { return $this->client; }
    public function setClient(?User $client): static { $this->client = $client; return $this; }

    public function getLignesCommande(): Collection { return $this->lignesCommande; }

    public function addLigneCommande(LigneCommande $ligne): static
    {
        if (!$this->lignesCommande->contains($ligne)) {
            $this->lignesCommande->add($ligne);
            $ligne->setCommande($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligne): static
    {
        if ($this->lignesCommande->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
        }
        return $this;
    }

    /**
     * Calcule le total de la commande automatiquement.
     */
    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->lignesCommande as $ligne) {
            $total += $ligne->getSousTotal();
        }
        return $total;
    }
}
