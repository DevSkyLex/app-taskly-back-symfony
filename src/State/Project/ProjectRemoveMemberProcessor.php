<?php

namespace App\State\Project;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Entity\User;
use App\Repository\ProjectInvitationRepository;
use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProjectRemoveMemberProcessor implements ProcessorInterface
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
  ): void {
    $admin = $this->security->getUser();

    if (!$admin instanceof User) {
      throw new AccessDeniedException(message: 'The user must be authenticated');
    }

    $project = $this->projectRepository->find(id: $uriVariables['project']);

    if (!$project instanceof Project) {
      throw new NotFoundHttpException(message: 'The project does not exist');
    }

    $user = $this->userRepository->find(id: $data['user']);

    if (!$user instanceof User) {
      throw new NotFoundHttpException(message: 'The user does not exist');
    }

    $member = $this->projectMemberRepository->findOneBy(criteria: [
      'project' => $project,
      'member' => $user
    ]);

    if (!$member instanceof ProjectMember) {
      throw new NotFoundHttpException(message: 'The user is not a member of the project');
    }

    $this->projectMemberRepository->delete(projectMember: $member);
  }
}