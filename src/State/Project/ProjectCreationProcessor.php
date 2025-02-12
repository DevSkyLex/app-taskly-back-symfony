<?php

namespace App\State\Project;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Enum\ProjectRole;
use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Entity\User;
use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ProjectCreationProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly ProjectRepository $projectRepository,
    private readonly ProjectMemberRepository $projectMemberRepository,
    private readonly Security $security
  ) {}

  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): Project {
    $user = $this->security->getUser();

    if (!$user instanceof User) {
      throw new AccessDeniedHttpException(message: 'The user must be authenticated');
    }

    if (!$data instanceof Project) {
      throw new BadRequestHttpException(message: 'The data must be an instance of Project');
    }

    $member = new ProjectMember();
    $member->setMember(member: $user);
    $member->setProject(project: $data);
    $member->setRole(role: ProjectRole::MANAGER);

    $data->addMember(member: $member);

    $this->projectRepository->save(project: $data);

    return $data;
  }
}