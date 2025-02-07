<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\DTO\UserRegisterDTO;
use App\Repository\UserRepository;
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

#[ApiResource(
  paginationEnabled: true,
  paginationClientItemsPerPage: true,
  normalizationContext: ['groups' => ['user:read']],
  denormalizationContext: ['groups' => ['user:write']],
  security: 'is_granted("ROLE_USER") or object == user',
)]
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
  #[Groups(groups: ['user:read', 'user:write'])]
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
  #[Groups(groups: ['user:read', 'user:write'])]
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
}
