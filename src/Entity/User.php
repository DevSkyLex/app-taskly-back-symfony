<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Enum\UserRole;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
  paginationEnabled: true,
  paginationClientItemsPerPage: true,
  normalizationContext: ['groups' => ['user:read']],
  denormalizationContext: ['groups' => ['user:write']],
  security: 'is_granted("ROLE_USER") or object == user',
)]
#[Vich\Uploadable]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['email' => 'partial'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  //#region Traits
  /**
   * Trait TimestampableEntity
   * 
   * Ce trait permet de gérer les dates de création et
   * de mise à jour d'une entité
   * 
   * @see TimestampableEntity
   */
  use TimestampableEntity;

  /**
   * Trait SoftDeleteableEntity
   * 
   * Ce trait permet de gérer la suppression
   * logique d'une entité
   * 
   * @see SoftDeleteableEntity
   */
  use SoftDeleteableEntity;
  //#endregion

  //#region Propriétés
  /**
   * Propriété id
   * 
   * Identifiant de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Uuid|null $id Identifiant de l'utilisateur
   */
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
  #[Groups(groups: ['user:read', 'user:write', 'project_member:read', 'project_invitation:read'])]
  private ?Uuid $id = null;

  /**
   * Propriété email
   * 
   * Adresse email de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $email Adresse email de l'utilisateur
   */
  #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
  #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
  #[Assert\NotBlank(message: 'Please enter an email')]
  #[Groups(groups: ['user:read', 'user:write', 'project_member:read', 'project_invitation:read'])]
  private ?string $email = null;

  /**
   * Propriété roles
   * 
   * Rôles de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var array $roles Rôles de l'utilisateur
   */
  #[ORM\Column(type: Types::JSON)]
  #[Assert\Choice(choices: [UserRole::class, 'cases'], multiple: true)]
  #[Groups(groups: ['user:read'])]
  private array $roles = [];

  /**
   * Propriété password
   * 
   * Mot de passe de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $password Mot de passe de l'utilisateur
   */
  #[ORM\Column(type: Types::STRING, length: 255)]
  #[Assert\NotBlank(message: 'Please enter a password')]
  #[Groups(groups: ['user:write'])]
  private ?string $password = null;

  /**
   * Propriété firstName
   * 
   * Prénom de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $firstName Prénom de l'utilisateur
   */
  #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
  #[Assert\NotBlank(message: 'Please enter a first name')]
  #[Assert\Length(
    min: 2,
    max: 50,
    minMessage: 'The first name must be at least {{ limit }} characters long',
    maxMessage: 'The first name cannot be longer than {{ limit }} characters'
  )]
  #[Groups(groups: ['user:read', 'user:write', 'project_member:read', 'project_invitation:read'])]
  private ?string $firstName = null;

  /**
   * Propriété lastName
   * 
   * Nom de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $lastName Nom de l'utilisateur
   */
  #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
  #[Assert\NotBlank(message: 'Please enter a last name')]
  #[Assert\Length(
    min: 2,
    max: 50,
    minMessage: 'The last name must be at least {{ limit }} characters long',
    maxMessage: 'The last name cannot be longer than {{ limit }} characters'
  )]
  #[Groups(groups: ['user:read', 'user:write', 'project_member:read', 'project_invitation:read'])]
  private ?string $lastName = null;

  /**
   * Propriété avatarFile
   * 
   * Fichier de l'avatar de l'utilisateur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var File|null $avatarFile Fichier de l'avatar de l'utilisateur
   */
  #[Assert\File(
    maxSize: '2M',
    mimeTypes: ['image/jpeg', 'image/png', 'image/jpg'],
    mimeTypesMessage: 'Please upload a valid image file (JPEG or PNG)'
  )]
  #[Vich\UploadableField(mapping: 'user_avatar', fileNameProperty: 'avatar')]
  #[Groups(groups: ['user:write'])]
  private ?File $avatarFile = null;

  #[ORM\OneToMany(mappedBy: 'invited', targetEntity: ProjectInvitation::class)]
  #[Groups(groups: ['user:read'])]
  private Collection $projectInvitations;

  #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
  #[Groups(groups: ['user:read', 'user:write'])]
  private ?string $avatar = null;
  //#endregion

  //#region Constructeur
  public function __construct()
  {
    $this->projectInvitations = new ArrayCollection();
  }
  //#endregion

  public function getId(): ?Uuid
  {
    return $this->id;
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

  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  public function getRoles(): array
  {
    $roles = $this->roles;
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
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

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  public function setFirstName(?string $firstName): static
  {
    $this->firstName = $firstName;

    return $this;
  }

  public function getLastName(): ?string
  {
    return $this->lastName;
  }

  public function setLastName(?string $lastName): static
  {
    $this->lastName = $lastName;

    return $this;
  }

  public function getAvatar(): ?string
  {
    return $this->avatar;
  }

  public function setAvatar(?string $avatar): static
  {
    $this->avatar = $avatar;

    return $this;
  }

  public function getAvatarFile(): ?File
  {
    return $this->avatarFile;
  }

  public function setAvatarFile(?File $avatarFile): static
  {
    $this->avatarFile = $avatarFile;

    if ($avatarFile) {
      $this->updatedAt = new DateTimeImmutable();
    }

    return $this;
  }

  public function getProjectInvitations(): Collection
  {
    return $this->projectInvitations;
  }

  public function addProjectInvitation(ProjectInvitation $invitation): static
  {
    if (!$this->projectInvitations->contains($invitation)) {
      $this->projectInvitations->add($invitation);
      $invitation->setInvited($this);
    }

    return $this;
  }

  public function removeProjectInvitation(ProjectInvitation $invitation): static
  {
    if ($this->projectInvitations->removeElement($invitation)) {
      // Set the owning side to null (unless already changed)
      if ($invitation->getInvited() === $this) {
        $invitation->setInvited(null);
      }
    }

    return $this;
  }
}
