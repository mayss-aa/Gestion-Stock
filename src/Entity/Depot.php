<?php

namespace App\Entity;

use App\Repository\DepotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepotRepository::class)]
class Depot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $nom_depot = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $localisation_depot = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]
    #[Assert\Positive( message: "nombre doit etre positif")]
    private ?float $capacite_depot = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]
    private ?string $unite_cap_depot = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $type_stockage_depot = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Champ ne peut pas être vide")]

    private ?string $statut_depot = null;

    /**
     * @var Collection<int, Ressource>
     */
    #[ORM\OneToMany(targetEntity: Ressource::class, mappedBy: 'depot' , cascade:["remove"])]
    private Collection $ressource;

    public function __construct()
    {
        $this->ressource = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDepot(): ?string
    {
        return $this->nom_depot;
    }

    public function setNomDepot(string $nom_depot): static
    {
        $this->nom_depot = $nom_depot;

        return $this;
    }

    public function getLocalisationDepot(): ?string
    {
        return $this->localisation_depot;
    }

    public function setLocalisationDepot(string $localisation_depot): static
    {
        $this->localisation_depot = $localisation_depot;

        return $this;
    }

    public function getCapaciteDepot(): ?float
    {
        return $this->capacite_depot;
    }

    public function setCapaciteDepot(float $capacite_depot): static
    {
        $this->capacite_depot = $capacite_depot;

        return $this;
    }

    public function getUniteCapDepot(): ?string
    {
        return $this->unite_cap_depot;
    }

    public function setUniteCapDepot(string $unite_cap_depot): static
    {
        $this->unite_cap_depot = $unite_cap_depot;

        return $this;
    }

    public function getTypeStockageDepot(): ?string
    {
        return $this->type_stockage_depot;
    }

    public function setTypeStockageDepot(string $type_stockage_depot): static
    {
        $this->type_stockage_depot = $type_stockage_depot;

        return $this;
    }

    public function getStatutDepot(): ?string
    {
        return $this->statut_depot;
    }

    public function setStatutDepot(string $statut_depot): static
    {
        $this->statut_depot = $statut_depot;

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessource(): Collection
    {
        return $this->ressource;
    }

    public function addRessource(Ressource $ressource): static
    {
        if (!$this->ressource->contains($ressource)) {
            $this->ressource->add($ressource);
            $ressource->setDepot($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): static
    {
        if ($this->ressource->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getDepot() === $this) {
                $ressource->setDepot(null);
            }
        }

        return $this;
    }
}
