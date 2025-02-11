<?php

namespace App\Repository;

use App\Entity\ProjectInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectInvitation>
 */
class ProjectInvitationRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct(
      registry: $registry,
      entityClass: ProjectInvitation::class
    );
  }

  public function save(ProjectInvitation $projectInvitation): void
  {
    $this->getEntityManager()->persist(object: $projectInvitation);
    $this->getEntityManager()->flush();
  }

  public function delete(ProjectInvitation $projectInvitation): void
  {
    $this->getEntityManager()->remove(object: $projectInvitation);
    $this->getEntityManager()->flush();
  }
}
