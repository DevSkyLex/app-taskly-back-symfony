<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProjectMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enum\ProjectRole;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectMemberRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_PROJECT', columns: ['member_id', 'project_id'])]
class ProjectMember
{
  //#region Propriétés
  #[ORM\Id]
  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'members')]
  #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false)]
  #[Groups(groups: ['project:read', 'project:write'])]
  private ?User $member = null;

  #[ORM\Id]
  #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'members')]
  #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
  #[Groups(groups: ['project:read', 'project:write'])]
  private ?Project $project = null;

  #[ORM\Column(type: 'string', length: 32, nullable: true)]
  #[Assert\Choice(choices: [ProjectRole::class, 'cases'])]
  #[Groups(groups: ['project:read', 'project:write'])]
  private ?string $role = null;
  //#endregion

  public function getProject(): ?Project
  {
    return $this->project;
  }

  public function setProject(?Project $project): static
  {
    $this->project = $project;

    return $this;
  }

  public function getMember(): ?User
  {
    return $this->member;
  }

  public function setMember(?User $member): static
  {
    $this->member = $member;

    return $this;
  }

  public function getRole(): ?string
  {
    return $this->role;
  }

  public function setRole(?string $role): static
  {
    $this->role = $role;

    return $this;
  }
}
