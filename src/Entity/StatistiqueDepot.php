<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\StatistiqueDepotRepository;

#[ORM\Entity(repositoryClass: StatistiqueDepotRepository::class)]
class StatistiqueDepot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Depot::class, inversedBy: "statistiques")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")] // Ajout du CASCADE ici
    private ?Depot $depot = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'integer')]
    private ?int $tauxRemplissage = null;

    // Getters et Setters
    public function getId(): ?int { return $this->id; }

    public function getDepot(): ?Depot { return $this->depot; }
    public function setDepot(?Depot $depot): self { $this->depot = $depot; return $this; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self { $this->date = $date; return $this; }

    public function getTauxRemplissage(): ?int { return $this->tauxRemplissage; }
    public function setTauxRemplissage(int $tauxRemplissage): self { $this->tauxRemplissage = $tauxRemplissage; return $this; }
}
