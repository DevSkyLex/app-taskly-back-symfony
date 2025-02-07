<?php

namespace App\State\Task;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\TaskRepository;

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
    private readonly TaskRepository $taskRepository
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
    return $this->taskRepository->findRootTasks();
  }
  //#endregion
}