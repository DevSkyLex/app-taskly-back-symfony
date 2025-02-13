<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Enum\ProjectInvitationStatus;
use App\Repository\ProjectInvitationRepository;
use App\State\Project\ProjectAcceptInvitationProcessor;
use App\State\Project\ProjectInviteProcessor;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
  shortName: 'Project Invitation',
  paginationEnabled: true,
  paginationClientItemsPerPage: true,
  normalizationContext: ['groups' => ['project_invitation:read']],
  denormalizationContext: ['groups' => ['project_invitation:write']],
  operations: [
    new Post(
      uriTemplate: '/projects/{project}/invitations/invite/{invited}',
      uriVariables: [
        'project' => new Link(
          fromClass: Project::class,
          toProperty: 'project'
        ),
        'invited'
      ],
      input: false,
      output: ProjectInvitation::class,
      processor: ProjectInviteProcessor::class,
      openapi: new Operation(
        summary: 'Invite a user to a project',
        description: 'Send an invitation to another user to join the project'
      )
    ),
    new Post(
      uriTemplate: '/projects/{project}/invitations/accept/{invitation}',
      uriVariables: [
        'project' => new Link(
          fromClass: Project::class,
          toProperty: 'project'
        ),
        'invitation' => new Link(
          fromClass: ProjectInvitation::class,
        )
      ],
      input: false,
      processor: ProjectAcceptInvitationProcessor::class,
      openapi: new Operation(
        summary: 'Join a project',
        description: 'Accept an invitation to join the project'
      )
    ),
  ]
)]
#[ORM\Entity(repositoryClass: ProjectInvitationRepository::class)]
class ProjectInvitation
{
  //#region Propriétés
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
  #[Groups(groups: ['user:read', 'user:write'])]
  private ?Uuid $id = null;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'projectInvitations')]
  #[ORM\JoinColumn(name: 'invited_id', nullable: false, referencedColumnName: 'id')]
  #[Assert\NotBlank(message: 'The invited user is required')]
  #[Groups(groups: ['project_invitation:read', 'project_invitation:write'])]
  private ?User $invited = null;

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(name: 'sender_id', nullable: false, referencedColumnName: 'id')]
  #[Assert\NotBlank(message: 'The sender user is required')]
  #[Groups(groups: ['project_invitation:read'])]
  private ?User $sender = null;

  #[ORM\ManyToOne(targetEntity: Project::class)]
  #[ORM\JoinColumn(name: 'project_id', nullable: false, referencedColumnName: 'id')]
  #[Assert\NotBlank(message: 'The project is required')]
  #[Groups(groups: ['project_invitation:read', 'project_invitation:write'])]
  private ?Project $project = null;

  #[ORM\Column(type: Types::STRING, enumType: ProjectInvitationStatus::class, nullable: false)]
  #[Assert\NotBlank(message: 'The status is required')]
  #[Groups(groups: ['project_invitation:read'])]
  private ProjectInvitationStatus $status = ProjectInvitationStatus::PENDING;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
  #[Assert\NotBlank(message: 'The expiration date is required')]
  #[Assert\GreaterThan(value: 'today', message: 'The expiration date must be greater than today')]
  #[Groups(groups: ['project_invitation:read'])]
  private ?DateTimeImmutable $expiresAt = null;
  //#endregion

  public function getId(): ?Uuid
  {
    return $this->id;
  }

  public function getInvited(): ?User
  {
    return $this->invited;
  }

  public function setInvited(?User $invited): static
  {
    $this->invited = $invited;

    return $this;
  }

  public function getSender(): ?User
  {
    return $this->sender;
  }

  public function setSender(?User $sender): static
  {
    $this->sender = $sender;

    return $this;
  }

  public function getProject(): ?Project
  {
    return $this->project;
  }

  public function setProject(?Project $project): static
  {
    $this->project = $project;

    return $this;
  }

  public function getStatus(): ProjectInvitationStatus
  {
    return $this->status;
  }

  public function setStatus(ProjectInvitationStatus $status): static
  {
    $this->status = $status;

    return $this;
  }

  public function getExpiresAt(): ?DateTimeImmutable
  {
    return $this->expiresAt;
  }

  public function setExpiresAt(DateTimeImmutable $expiresAt): static
  {
    $this->expiresAt = $expiresAt;

    return $this;
  }
}
