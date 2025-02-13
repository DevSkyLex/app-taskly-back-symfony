<?php

namespace App\State\Task;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaskCreationProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly TaskRepository $taskRepository,
    private readonly ProjectRepository $projectRepository,
    private readonly Security $security
  ) {}

  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): Task {
    $user = $this->security->getUser();

    if (!$user instanceof User) {
      throw new AccessDeniedHttpException(message: 'The user must be authenticated');
    }

    if (!$data instanceof Task) {
      throw new BadRequestHttpException(message: 'The data must be an instance of Task');
    }

    $project = $this->projectRepository->find(id: $uriVariables['project']);
    if (!$project instanceof Project) {
      throw new NotFoundHttpException(message: 'The project does not exist');
    }

    $data->setProject(project: $project);

    $this->taskRepository->save(task: $data);

    return $data;
  }
}