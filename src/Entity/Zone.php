<?php

namespace App\Entity;

use App\Repository\ZoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
class Zone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la zone ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: "/^[A-Za-zÀ-ÿ\s]+$/",
        message: "Le nom de la zone ne peut contenir que des lettres."
    )]
    private ?string $nom_zone = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Veuillez entrer une superficie.")]
    #[Assert\Positive(message: "La superficie doit être un nombre positif.")]
    private ?float $superficie = null;

    #[ORM\ManyToOne(inversedBy: 'zones')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'zone')]
    #[Assert\NotNull(message: "Veuillez sélectionner une plante.")]
    private ?Plante $plante = null;

    #[ORM\ManyToOne(inversedBy: 'zones')]
    private ?Intervention $intervention = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomZone(): ?string
    {
        return $this->nom_zone;
    }

    public function setNomZone(string $nom_zone): static
    {
        $this->nom_zone = $nom_zone;
        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getPlante(): ?Plante
    {
        return $this->plante;
    }

    public function setPlante(?Plante $plante): static
    {
        $this->plante = $plante;
        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): static
    {
        $this->intervention = $intervention;
        return $this;
    }
}
