<?php

namespace App\Entity;

use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_machine = null;

    #[ORM\Column(length: 255)]
    private ?string $etat_machine = null;

    #[ORM\Column(length: 255)]
    private ?string $brand_machine = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_achat = null;

    /**
     * @var Collection<int, Maintenance>
     */
    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'machine')]
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

    public function setNomMachine(string $nom_machine): static
    {
        $this->nom_machine = $nom_machine;

        return $this;
    }

    public function getEtatMachine(): ?string
    {
        return $this->etat_machine;
    }

    public function setEtatMachine(string $etat_machine): static
    {
        $this->etat_machine = $etat_machine;

        return $this;
    }

    public function getBrandMachine(): ?string
    {
        return $this->brand_machine;
    }

    public function setBrandMachine(string $brand_machine): static
    {
        $this->brand_machine = $brand_machine;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->date_achat;
    }

    public function setDateAchat(\DateTimeInterface $date_achat): static
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

    public function addMaintenance(Maintenance $maintenance): static
    {
        if (!$this->maintenance->contains($maintenance)) {
            $this->maintenance->add($maintenance);
            $maintenance->setMachine($this);
        }

        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): static
    {
        if ($this->maintenance->removeElement($maintenance)) {
            // set the owning side to null (unless already changed)
            if ($maintenance->getMachine() === $this) {
                $maintenance->setMachine(null);
            }
        }

        return $this;
    }
}
