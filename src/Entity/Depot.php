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
    #[Assert\Positive(message: "Le nombre doit être positif")]
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

    // Jointure avec Ressource
    #[ORM\OneToMany(targetEntity: Ressource::class, mappedBy: 'depot', cascade: ["remove"])]
    private Collection $ressources;
    #[ORM\Column]
    private ?int $limitedby = null;

    #[ORM\Column]
    private ?bool $isshown = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $utilisation_actuelle = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $taux_augmentation = null;

    public function __construct()
    {
        $this->ressources = new ArrayCollection();
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

    public function getUtilisationActuelle(): ?float
    {
        return $this->utilisation_actuelle;
    }

    public function setUtilisationActuelle(?float $utilisation_actuelle): static
    {
        $this->utilisation_actuelle = $utilisation_actuelle;
        return $this;
    }

    public function getTauxAugmentation(): ?float
    {
        return $this->taux_augmentation;
    }

    public function setTauxAugmentation(?float $taux_augmentation): static
    {
        $this->taux_augmentation = $taux_augmentation;
        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }










    public function getLimitedby(): ?int
    {
        return $this->limitedby;
    }

    public function setLimitedby(int $limitedby): static
    {
        $this->limitedby = $limitedby;

        return $this;
    }






    public function calculateTauxAugmentation(): float
    {
        $ressources = $this->getRessources();
        if (count($ressources) < 1) {
            return 0;
        }

        $ressourcesArray = $ressources->toArray();
        usort($ressourcesArray, fn($a, $b) => $a->getDateAjoutRessource() <=> $b->getDateAjoutRessource());

        $totalQuantiteM3 = array_reduce($ressourcesArray, function ($carry, $ressource) {
            return $carry + $this->convertToM3($ressource->getQuantiteRessource(), $ressource->getUniteMesure());
        }, 0);

        $firstDate = $ressourcesArray[0]->getDateAjoutRessource();
        $lastDate = end($ressourcesArray)->getDateAjoutRessource();
        $joursTotaux = max(1, $lastDate->diff($firstDate)->days);

        return $totalQuantiteM3 / $joursTotaux;
    }

    public function addRessource(Ressource $ressource): static
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources->add($ressource);
            $ressource->setDepot($this);
        }
        return $this;
    }

    public function removeRessource(Ressource $ressource): static
    {
        if ($this->ressources->removeElement($ressource)) {
            if ($ressource->getDepot() === $this) {
                $ressource->setDepot(null);
            }
        }
        return $this;
    }

    // ✅ Fonction pour convertir les valeurs en m³
    public function convertToM3(float $quantite, string $unite): float
    {
        if ($quantite <= 0) {
            return 0.0;
        }
    
        $unite = trim($unite); // Supprimer les espaces inutiles
    
        return match ($unite) {
            'kg', 'L' => $quantite * 0.001,
            'm3', 'm³' => $quantite, // Ajout de "m3" pour compatibilité
            default => 0.0
        };
    }
    

    // ✅ Récupérer les valeurs converties en m³
    public function getCapaciteEnM3(): float
{
    return $this->convertToM3(floatval($this->getCapaciteDepot()), trim($this->getUniteCapDepot()));
}


    public function getUtilisationEnM3(): float
    {
        return $this->utilisation_actuelle !== null ? $this->convertToM3($this->utilisation_actuelle, $this->unite_cap_depot) : 0.0;
    }

    public function getTauxAugmentationEnM3(): float
    {
        return $this->taux_augmentation !== null ? $this->convertToM3($this->taux_augmentation, $this->unite_cap_depot) : 0.0;
    }

    public function __toString()
    {
        return " capacité disponible : ". $this-> nom_depot . " capacité disponible : ".$this->capacite_depot ;
    }

    public function getIsshown(): ?bool
    {
        return $this->isshown;
    }
    
    public function setIsshown(bool $isshown): static
    {
        $this->isshown = $isshown;
        return $this;
    }
    

    public function hideRessources(): void
    {
        foreach ($this->ressources as $ressource) {
            $ressource->setIsshown(false);  // Met toutes les ressources du dépôt en invisible
        }
    }
    




}
