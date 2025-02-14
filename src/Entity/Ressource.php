<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: RessourceRepository::class)]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $nom_ressource = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $type_ressource = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]
    #[Assert\GreaterThan(value: 0, message: "nombre doit etre positif")]

    private ?float $quantite_ressource = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $unite_mesure = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]  // Autoriser NULL dans la BDD
    #[Assert\NotNull(message: "Champ ne peut pas être vide")]
    #[Assert\GreaterThanOrEqual("today", message: "La date d'ajout doit être aujourd'hui ou dans le futur")]
    private ?\DateTimeInterface $date_ajout_ressource = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: "Champ ne peut pas être vide")]

    #[Assert\GreaterThanOrEqual("today", message: "La date d'expiration doit être aujourd'hui ou dans le futur")]
    private ?\DateTimeInterface $date_expiration_ressource = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $statut_ressource = null;

    #[ORM\ManyToOne(inversedBy: 'ressource')]
 
    private ?Depot $depot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRessource(): ?string
    {
        return $this->nom_ressource;
    }

    public function setNomRessource(string $nom_ressource): static
    {
        $this->nom_ressource = $nom_ressource;

        return $this;
    }

    public function getTypeRessource(): ?string
    {
        return $this->type_ressource;
    }

    public function setTypeRessource(string $type_ressource): static
    {
        $this->type_ressource = $type_ressource;

        return $this;
    }

    public function getQuantiteRessource(): ?float
    {
        return $this->quantite_ressource;
    }

    public function setQuantiteRessource(float $quantite_ressource): static
    {
        $this->quantite_ressource = $quantite_ressource;

        return $this;
    }

    public function getUniteMesure(): ?string
    {
        return $this->unite_mesure;
    }

    public function setUniteMesure(string $unite_mesure): static
    {
        $this->unite_mesure = $unite_mesure;

        return $this;
    }

    public function getDateAjoutRessource(): ?\DateTimeInterface
    {
        return $this->date_ajout_ressource;
    }

    public function setDateAjoutRessource(\DateTimeInterface $date_ajout_ressource): static
    {
        $this->date_ajout_ressource = $date_ajout_ressource;

        return $this;
    }

    public function getDateExpirationRessource(): ?\DateTimeInterface
    {
        return $this->date_expiration_ressource;
    }

    public function setDateExpirationRessource(?\DateTimeInterface $date_expiration_ressource): static
    {
        $this->date_expiration_ressource = $date_expiration_ressource;

        return $this;
    }

    public function getStatutRessource(): ?string
    {
        return $this->statut_ressource;
    }

    public function setStatutRessource(string $statut_ressource): static
    {
        $this->statut_ressource = $statut_ressource;

        return $this;
    }

    public function getDepot(): ?Depot
    {
        return $this->depot;
    }

    public function setDepot(?Depot $depot): static
    {
        $this->depot = $depot;

        return $this;
    }
     /**
     * Validation personnalisée : `date_expiration_ressource` doit être ≥ `date_ajout_ressource`
     */
    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->date_ajout_ressource && $this->date_expiration_ressource) {
            if ($this->date_ajout_ressource > $this->date_expiration_ressource) {
                $context->buildViolation("La date d'expiration doit être après la date d'ajout.")
                    ->atPath('date_expiration_ressource')
                    ->addViolation();
            }
        }
    }
}
