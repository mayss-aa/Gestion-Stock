<?php

namespace App\Entity;

use App\Repository\MaintenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_maintenance = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $cout_maintenance = null;

    #[ORM\ManyToOne(inversedBy: 'maintenance')]
    private ?Machine $machine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMaintenance(): ?\DateTimeInterface
    {
        return $this->date_maintenance;
    }

    public function setDateMaintenance(\DateTimeInterface $date_maintenance): static
    {
        $this->date_maintenance = $date_maintenance;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCoutMaintenance(): ?float
    {
        return $this->cout_maintenance;
    }

    public function setCoutMaintenance(float $cout_maintenance): static
    {
        $this->cout_maintenance = $cout_maintenance;

        return $this;
    }

    public function getMachine(): ?Machine
    {
        return $this->machine;
    }

    public function setMachine(?Machine $machine): static
    {
        $this->machine = $machine;

        return $this;
    }
}
