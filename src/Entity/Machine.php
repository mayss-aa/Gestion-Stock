<?php

namespace App\Entity;

use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{
    public const ETAT_CHOICES = ['nouvelle', 'obsolete', 'a_reparer'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Le nom de la machine est obligatoire.")]
    #[Assert\Length(
        max: 30,
        maxMessage: "Le nom de la machine ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom_machine = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "L'état de la machine est obligatoire.")]
    #[Assert\Choice(
        choices: self::ETAT_CHOICES,
        message: "L'état de la machine doit être 'nouvelle', 'obsolète' ou 'à réparer'."
    )]
    private ?string $etat_machine = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "La marque de la machine est obligatoire.")]
    #[Assert\Length(
        max: 30,
        maxMessage: "La marque de la machine ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $brand_machine = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date d'achat est obligatoire.")]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: "Veuillez entrer une date valide."
    )]
    private ?\DateTimeInterface $date_achat = null;

    /**
     * @var Collection<int, Maintenance>
     */
    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'machine', cascade: ['remove'], orphanRemoval: true)]
    private Collection $maintenance;

    public function __construct()
    {
        $this->maintenance = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMachine(): ?string
    {
        return $this->nom_machine;
    }

    public function setNomMachine(string $nom_machine): self
    {
        $this->nom_machine = $nom_machine;
        return $this;
    }

    public function getEtatMachine(): ?string
    {
        return $this->etat_machine;
    }

    public function setEtatMachine(string $etat_machine): self
    {
        if (!in_array($etat_machine, self::ETAT_CHOICES, true)) {
            throw new \InvalidArgumentException("État invalide.");
        }
        $this->etat_machine = $etat_machine;
        return $this;
    }

    public function getBrandMachine(): ?string
    {
        return $this->brand_machine;
    }

    public function setBrandMachine(string $brand_machine): self
    {
        $this->brand_machine = $brand_machine;
        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->date_achat;
    }

    public function setDateAchat(\DateTimeInterface $date_achat): self
    {
        $this->date_achat = $date_achat;
        return $this;
    }

    /**
     * @return Collection<int, Maintenance>
     */
    public function getMaintenance(): Collection
    {
        return $this->maintenance;
    }

    public function addMaintenance(Maintenance $maintenance): self
    {
        if (!$this->maintenance->contains($maintenance)) {
            $this->maintenance->add($maintenance);
            $maintenance->setMachine($this);
        }
        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): self
    {
        if ($this->maintenance->removeElement($maintenance)) {
            if ($maintenance->getMachine() === $this) {
                $maintenance->setMachine(null);
            }
        }
        return $this;
    }
}
