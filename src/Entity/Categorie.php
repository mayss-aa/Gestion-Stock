<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[Assert\NotBlank(message: "Le nom de la catégorie est requis")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères"
    )]
    #[ORM\Id]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $nom_categorie = null;

    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description_categorie = null;

    #[Assert\Length(
        max: 50,
        maxMessage: "La saison de récolte ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Choice(
        choices: ['printemps', 'été', 'automne', 'hiver'],
        message: "La saison de récolte doit être parmi les valeurs suivantes: printemps, été, automne, hiver"
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $saisonDeRecolte = null;

    #[Assert\Length(
        max: 50,
        maxMessage: "La température idéale ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^\d+-\d+°C$/",
        message: "La température doit être au format 'XX-XX°C'"
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $temperatureIdeale = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'categorie')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getNomCategorie(): ?string
    {
        return $this->nom_categorie;
    }

    public function setNomCategorie(string $nom_categorie): static
    {
        $this->nom_categorie = $nom_categorie;

        return $this;
    }

    public function getDescriptionCategorie(): ?string
    {
        return $this->description_categorie;
    }

    public function setDescriptionCategorie(?string $description_categorie): static
    {
        $this->description_categorie = $description_categorie;

        return $this;
    }

    public function getSaisonDeRecolte(): ?string
    {
        return $this->saisonDeRecolte;
    }

    public function setSaisonDeRecolte(?string $saisonDeRecolte): static
    {
        $this->saisonDeRecolte = $saisonDeRecolte;
        return $this;
    }

    public function getTemperatureIdeale(): ?string
    {
        return $this->temperatureIdeale;
    }

    public function setTemperatureIdeale(?string $temperatureIdeale): static
    {
        $this->temperatureIdeale = $temperatureIdeale;
        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

        return $this;
    }
}
