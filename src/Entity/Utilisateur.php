<?php
namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Utilisateur implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: 'Veuillez entrer un email valide.')]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide.')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le genre ne peut pas être vide.')]
    private ?string $genre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de naissance ne peut pas être vide.')]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone ne peut pas être vide.')]
    #[Assert\Length(max: 15, maxMessage: 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $numTel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce mot de passe ne peut pas être vide.')]
    #[Assert\Length(min: 6, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creeLe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $misAJourLe = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(targetEntity: Zone::class, mappedBy: 'utilisateur')]
    private Collection $zones;

    // Not mapped to the database: Used for file uploads
    private ?File $photoFile = null;

    public function __construct()
    {
        $this->zones = new ArrayCollection();
        $this->creeLe = new \DateTimeImmutable();
        $this->misAJourLe = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function setCreationTimestamp(): void
    {
        $this->creeLe = new \DateTimeImmutable();
        $this->misAJourLe = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->misAJourLe = new \DateTimeImmutable();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }

    public function setNumTel(string $numTel): static
    {
        $this->numTel = $numTel;
        return $this;
    }

    public function getCreeLe(): ?\DateTimeInterface
    {
        return $this->creeLe;
    }

    public function getMisAJourLe(): ?\DateTimeInterface
    {
        return $this->misAJourLe;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }
    

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
    public function getUsernameIdentifier(): string
    {
        return sprintf('%s %s', $this->nom, $this->prenom);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = [''];
        if ($this->role) {
            $roles[] = $this->role->getNomRole();
        }
        return array_unique($roles);
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }
        if ($this->getEmail() !== $user->getEmail()) {
            return false;
        }
        return $this->getRoles() === $user->getRoles();
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary sensitive data on the user, clear it here
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?File $photoFile): self
    {
        $this->photoFile = $photoFile;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;
        return $this;
    }
}
