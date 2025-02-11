<?php

namespace App\Repository;

use App\Entity\ProjectMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectMember>
 */
class ProjectMemberRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct(
      registry: $registry,
      entityClass: ProjectMember::class
    );
  }

  public function save(ProjectMember $projectMember): void
  {
    $this->getEntityManager()->persist(object: $projectMember);
    $this->getEntityManager()->flush();
  }

  public function delete(ProjectMember $projectMember): void
  {
    $this->getEntityManager()->remove(object: $projectMember);
    $this->getEntityManager()->flush();
  }
}
