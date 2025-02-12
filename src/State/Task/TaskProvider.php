<?php

namespace App\State\Task;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaskProvider implements ProviderInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le provider des tâches
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param TaskRepository $taskRepository
   */
  public function __construct(
    private readonly TaskRepository $taskRepository,
    private readonly ProjectRepository $projectRepository,
    private readonly Security $security
  ) {}
  //#endregion

  //#region Méthodes
  /**
   * Méthode provide
   * 
   * Fournit les tâches racines
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Operation $operation Opération
   * @param array $uriVariables Variables d'URI
   * @param array $context Contexte
   * 
   * @return array Tâches racines
   */
  public function provide(
    Operation $operation, 
    array $uriVariables = [], 
    array $context = []
  ): array
  {
    $user = $this->security->getUser();
    if (!$user instanceof User) {
      throw new AccessDeniedHttpException(message: 'The user must be authenticated.');
    }

    $existingProject = $this->projectRepository->exists(id: $uriVariables['project']);
    if (!$existingProject) {
      throw new NotFoundHttpException(message: 'The project does not exist.');
    }

    $membership = $this->projectRepository->isMember(
      projectId: $uriVariables['project'],
      userId: $user->getId()
    );
    if (!$membership) {
      throw new AccessDeniedHttpException(message: 'The user is not a member of the project.');
    }

    $tasks = $this->taskRepository->findRootTasksByProjectId(projectId: $uriVariables['project']);

    return $tasks;
  }
  //#endregion
}