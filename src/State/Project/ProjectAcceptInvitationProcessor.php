<?php

namespace App\State\Project;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Enum\ProjectInvitationStatus;
use App\Entity\Project;
use App\Entity\ProjectInvitation;
use App\Entity\ProjectMember;
use App\Entity\User;
use App\Repository\ProjectInvitationRepository;
use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;

final class ProjectAcceptInvitationProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly ProjectRepository $projectRepository,
    private readonly UserRepository $userRepository,
    private readonly ProjectMemberRepository $projectMemberRepository,
    private readonly ProjectInvitationRepository $projectInvitationRepository,
    private readonly Security $security
  ) {}

  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): ProjectMember {
    $user = $this->security->getUser();

    if (!$user instanceof User) {
      throw new LogicException(message: 'The user must be authenticated');
    }

    $project = $this->projectRepository->find(id: $uriVariables['project']);
    if (!$project instanceof Project) {
      throw new LogicException(message: 'The project does not exist');
    }

    $invitation = $this->projectInvitationRepository->findOneBy(criteria: [
      'id' => $uriVariables['invitation'],
      'status' => ProjectInvitationStatus::PENDING
    ]);

    if (!$invitation instanceof ProjectInvitation) {
      throw new LogicException(message: 'The invitation already exists');
    }

    if ($invitation->getExpiresAt() < new DateTimeImmutable()) {
      $this->projectInvitationRepository->delete(projectInvitation: $invitation);
      throw new LogicException(message: 'The invitation has expired');
    }

    $invitation->setStatus(status: ProjectInvitationStatus::ACCEPTED);

    $member = new ProjectMember();
    $member->setProject(project: $project);
    $member->setMember(member: $user);
    
    $this->projectMemberRepository->save(projectMember: $member);

    return $member;
  }
}