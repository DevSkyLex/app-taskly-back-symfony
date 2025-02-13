<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
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
  public function save(Task $task): void
  {
    $this->getEntityManager()->persist($task);
    $this->getEntityManager()->flush();
  }

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
      ->where( 't.project = :projectId')
      ->andWhere('t.parent IS NULL')
      ->setParameter(key: 'projectId', value: $projectId)
      ->getQuery()
      ->getResult();
  }
  //#endregion
}
