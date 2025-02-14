<?php

namespace App\Entity;

use App\Repository\PlanteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanteRepository::class)]
class Plante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_plante = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_plantation = null;

    #[ORM\Column]
    private ?float $rendement_estime = null;

    /**
     * @var Collection<int, Zone>
     */
    #[ORM\OneToMany(targetEntity: Zone::class, mappedBy: 'plante')]
    private Collection $zone;

    public function __construct()
    {
        $this->zone = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPlante(): ?string
    {
        return $this->nom_plante;
    }

    public function setNomPlante(string $nom_plante): static
    {
        $this->nom_plante = $nom_plante;

        return $this;
    }

    public function getDatePlantation(): ?\DateTimeInterface
    {
        return $this->date_plantation;
    }

    public function setDatePlantation(\DateTimeInterface $date_plantation): static
    {
        $this->date_plantation = $date_plantation;

        return $this;
    }

    public function getRendementEstime(): ?float
    {
        return $this->rendement_estime;
    }

    public function setRendementEstime(float $rendement_estime): static
    {
        $this->rendement_estime = $rendement_estime;

        return $this;
    }

    /**
     * @return Collection<int, Zone>
     */
    public function getZone(): Collection
    {
        return $this->zone;
    }

    public function addZone(Zone $zone): static
    {
        if (!$this->zone->contains($zone)) {
            $this->zone->add($zone);
            $zone->setPlante($this);
        }

        return $this;
    }

    public function removeZone(Zone $zone): static
    {
        if ($this->zone->removeElement($zone)) {
            // set the owning side to null (unless already changed)
            if ($zone->getPlante() === $this) {
                $zone->setPlante(null);
            }
        }

        return $this;
    }
}
