<?php

namespace App\State\Project;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ProjectMemberProvider implements ProviderInterface
{
  public function __construct(
    private readonly ProjectMemberRepository $projectMemberRepository,
    private readonly ProjectRepository $projectRepository,
    private readonly Security $security
  ) {
  }

  public function provide(
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): Collection {
    $user = $this->security->getUser();
    if (!$user instanceof User) {
      throw new NotFoundHttpException(message: 'The user must be authenticated.');
    }

    $project = $this->projectRepository->find(id: $uriVariables['project']);
    if (!$project) {
      throw new NotFoundHttpException(message: 'The project does not exist.');
    }

    $membership = $this->projectRepository->isMember(
      projectId: $project->getId(), 
      userId: $user->getId()
    );

    if (!$membership) {
      throw new AccessDeniedHttpException(message: 'You are not a member of this project.');
    }

    $members = $project->getMembers();

    return $members;
  }
}