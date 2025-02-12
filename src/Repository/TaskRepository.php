<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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

  public function findTasksByProjectId(Uuid $projectId): array
  {
    return $this->createQueryBuilder(alias: 't')
      ->where(predicates: 't.project = :projectId')
      ->setParameter(key: 'projectId', value: $projectId)
      ->getQuery()
      ->getResult();
  }

  public function findRootTasksByProjectId(Uuid $projectId): array
  {
    return $this->createQueryBuilder(alias: 't')
      ->where('t.parent IS NULL')
      ->andWhere( 't.project = :projectId')
      ->setParameter(key: 'projectId', value: $projectId)
      ->getQuery()
      ->getResult();
  }
  //#endregion
}
