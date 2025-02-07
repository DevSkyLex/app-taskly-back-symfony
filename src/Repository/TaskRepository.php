<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct(
      registry: $registry,
      entityClass: Task::class
    );
  }

  //#region Méthodes
  public function findRootTasks(): array
  {
    return $this->createQueryBuilder(alias: 't')
      ->where('t.parent IS NULL')
      ->getQuery()
      ->getResult();
  }
  //#endregion
}
