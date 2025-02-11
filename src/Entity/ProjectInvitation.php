<?php

namespace App\Entity;

use App\Entity\Enum\ProjectInvitationStatus;
use App\Repository\ProjectInvitationRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectInvitationRepository::class)]
class ProjectInvitation
{
  //#region Propriétés
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
  #[Groups(groups: ['project_invitation:read'])]
  private ?Uuid $id = null;

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(nullable: false)]
  #[Assert\NotBlank(message: 'The invited user is required')]
  #[Groups(groups: ['project_invitation:read', 'project_invitation:write'])]
  private ?User $invited = null;

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(nullable: false)]
  #[Assert\NotBlank(message: 'The sender user is required')]
  #[Groups(groups: ['project_invitation:read'])]
  private ?User $sender = null;

  #[ORM\ManyToOne(targetEntity: Project::class)]
  #[ORM\JoinColumn(nullable: false)]
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
