<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Project;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class ProjectMemberExtension implements QueryCollectionExtensionInterface
{
  public function __construct(
    private readonly Security $security
  ) {}

  public function applyToCollection(
    QueryBuilder $queryBuilder,
    QueryNameGeneratorInterface $queryNameGenerator,
    string $resourceClass, 
    Operation $operation = null, 
    array $context = []
  ): void {
    $this->addWhere(
      queryBuilder: $queryBuilder,
      resourceClass: $resourceClass
    );
  }

  public function applyToItem(
    QueryBuilder $queryBuilder, 
    QueryNameGeneratorInterface $queryNameGenerator,
    string $resourceClass, 
    array $identifiers, 
    Operation $operation = null, 
    array $context = []
  ): void {
    $this->addWhere(
      queryBuilder: $queryBuilder, 
      resourceClass: $resourceClass
    );
  }

  private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
  {
    if (Project::class !== $resourceClass) {
      return;
    }

    $user = $this->security->getUser();

    // Vérifier si l'utilisateur est connecté et est Manager pour accéder à la liste des invitations
    if (!$user) {
      return;
    }

    $rootAlias = $queryBuilder->getRootAliases()[0];
    $queryBuilder
      ->join(sprintf('%s.members', $rootAlias), 'm')
      ->andWhere('m.member = :current_user')
      ->setParameter('current_user', $user);
  }
}