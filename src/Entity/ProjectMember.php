<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProjectMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enum\ProjectRole;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use App\State\Project\ProjectLeaveProcessor;
use App\State\Project\ProjectMemberProvider;
use App\State\Project\ProjectRemoveMemberProcessor;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model\Operation;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
  shortName: 'Project Member',
  uriTemplate: '/projects/{project}/members',
  uriVariables: [
    'project' => new Link(
      fromClass: Project::class,
      toProperty: 'project'
    ),
  ],
  paginationEnabled: true,
  paginationClientItemsPerPage: true,
  normalizationContext: ['groups' => ['project_member:read']],
  denormalizationContext: ['groups' => ['project_member:write']],
  operations: [
    new GetCollection(
      uriTemplate: '/projects/{project}/members',
      input: false,
      output: ProjectMember::class,
      provider: ProjectMemberProvider::class,
      normalizationContext: ['groups' => ['project_member:read']],
      openapi: new Operation(
        summary: 'List project members',
        description: 'Get the list of members for a specific project'
      )
    ),
    new Delete(
      uriTemplate: '/projects/{project}/members/leave',
      input: false,
      processor: ProjectLeaveProcessor::class,
      openapi: new Operation(
        summary: 'Leave a project',
        description: 'Allow a user to leave a project'
      )
    ),
    new Delete(
      uriTemplate: '/projects/{project}/members/{user}',
      security: 'is_granted(\'ROLE_USER\') and object.isManager()',
      input: false,
      processor: ProjectRemoveMemberProcessor::class,
      openapi: new Operation(
        summary: 'Remove a member from the project',
        description: 'Admin can remove a member from the project'
      )
    ),
  ]
)]
#[ORM\Entity(repositoryClass: ProjectMemberRepository::class)]
class ProjectMember
{
  //#region Propriétés
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
  #[Groups(groups: ['user:read', 'user:write', 'project:read'])]
  private ?Uuid $id = null;

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false)]
  #[Groups(groups: ['project_member:read', 'project_member:write', 'project:read'])]
  private ?User $member = null;

  #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'members')]
  #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
  #[Groups(groups: ['project_member:read', 'project_member:write'])]
  private ?Project $project = null;

  #[ORM\Column(type: Types::STRING, enumType: ProjectRole::class, length: 32, nullable: false)]
  #[Groups(groups: ['project_member:read', 'project_member:write', 'project:read'])]
  private ProjectRole $role = ProjectRole::CONTRIBUTOR;
  //#endregion

  public function getId(): ?Uuid
  {
    return $this->id;
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

  public function getMember(): ?User
  {
    return $this->member;
  }

  public function setMember(?User $member): static
  {
    $this->member = $member;

    return $this;
  }

  public function getRole(): ?ProjectRole
  {
    return $this->role;
  }

  public function setRole(?ProjectRole $role): static
  {
    $this->role = $role;

    return $this;
  }

  public function isManager(): bool
  {
    return $this->role === ProjectRole::MANAGER;
  }
}
