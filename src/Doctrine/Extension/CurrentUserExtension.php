<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Project;
use App\Entity\ProjectInvitation;
use App\Entity\Task;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Classe CurrentUserExtension
 * @final
 * 
 * Permet de filtrer des ressources en fonction de 
 * l'utilisateur courant
 * 
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final readonly class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise les dépendances de la classe
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Security $security
   */
  public function __construct(
    private readonly Security $security
  ) {
  }
  //#endregion

  //#region Méthodes
  /**
   * Méthode applyToCollection
   * 
   * Applique le filtre sur une collection de 
   * ressources
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param QueryBuilder $queryBuilder
   * @param QueryNameGeneratorInterface $queryNameGenerator
   * @param string $resourceClass
   * @param Operation|null $operation
   * @param array $context
   * 
   * @return void Ne retourne rien
   */
  public function applyToCollection(
    QueryBuilder $queryBuilder,
    QueryNameGeneratorInterface $queryNameGenerator,
    string $resourceClass,
    Operation $operation = null,
    array $context = []
  ): void {
    $user = $this->security->getUser();
    if (!$user)
      return;

    $rootAlias = $queryBuilder->getRootAliases()[0];

    /**
     * Filtrage des projets
     * 
     * Seul les projets dont l'utilisateur courant 
     * est membre sont retournés
     * 
     * @since 1.0.0
     */
    if (Project::class === $resourceClass) {
      $queryBuilder
        ->join(sprintf('%s.members', $rootAlias), 'm')
        ->andWhere('m.member = :current_user')
        ->setParameter('current_user', $user);

      return;
    }

    /**
     * Filtrage des invitations
     * 
     * Seul les invitations dont l'utilisateur courant 
     * est destinataire sont retournées
     * 
     * @since 1.0.0
     */
    if (ProjectInvitation::class === $resourceClass) {
      $queryBuilder
        ->andWhere(sprintf('%s.invited = :current_user', $rootAlias))
        ->setParameter('current_user', $user);

      return;
    }

    /**
     * Filtrage des tâches
     * 
     * Seul les tâches des projets dont l'utilisateur 
     * courant est membre sont retournées
     * 
     * @since 1.0.0
     */
    if (Task::class === $resourceClass) {
      $queryBuilder
        ->join(sprintf('%s.project', $rootAlias), 'p')
        ->join('p.members', 'm')
        ->andWhere('m.member = :current_user')
        ->setParameter('current_user', $user);

      return;
    }
  }

  /**
   * Méthode applyToItem
   * 
   * Applique le filtre sur une ressource
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param QueryBuilder $queryBuilder
   * @param QueryNameGeneratorInterface $queryNameGenerator
   * @param string $resourceClass
   * @param array $identifiers
   * @param Operation|null $operation
   * @param array $context
   * 
   * @return void Ne retourne rien
   */
  public function applyToItem(
    QueryBuilder $queryBuilder,
    QueryNameGeneratorInterface $queryNameGenerator,
    string $resourceClass,
    array $identifiers,
    Operation $operation = null,
    array $context = []
  ): void {
    $user = $this->security->getUser();
    if (!$user)
      return;

    $rootAlias = $queryBuilder->getRootAliases()[0];

    /**
     * Filtrage du projet
     * 
     * Seul les projets dont l'utilisateur courant
     * est membre sont retournés
     * 
     * @since 1.0.0
     */
    if (Project::class === $resourceClass && isset($identifiers['project'])) {
      $queryBuilder
        ->join("$rootAlias.members", "m")
        ->andWhere("m.member = :current_user")
        ->andWhere("$rootAlias.id = :project_id") // 🔹 Filtrer sur l'ID spécifique
        ->setParameter("current_user", $user)
        ->setParameter("project_id", $identifiers['id']);

      return;
    }

    /**
     * Filtrage de l'invitation
     * 
     * Seul les invitations dont l'utilisateur courant
     * est destinataire sont retournées
     * 
     * @since 1.0.0
     */
    if (ProjectInvitation::class === $resourceClass && isset($identifiers['invitation'])) {
      $queryBuilder
        ->andWhere("$rootAlias.invited = :current_user")
        ->andWhere("$rootAlias.id = :invitation_id")
        ->setParameter("current_user", $user)
        ->setParameter("invitation_id", $identifiers['id']);

      return;
    }

    /**
     * Filtrage de la tâche
     * 
     * Seul les tâches des projets dont l'utilisateur
     * courant est membre sont retournées
     * 
     * @since 1.0.0
     */
    if (Task::class === $resourceClass && isset($identifiers['task'])) {
      $queryBuilder
          ->innerJoin("$rootAlias.project", "p")
          ->innerJoin("p.members", "m")    
          ->andWhere("m.member = :current_user")
          ->andWhere("$rootAlias.id = :task_id")
          ->setParameter("current_user", $user)
          ->setParameter("task_id", $identifiers['id'])
          ->setMaxResults(1);

      return;
    }

    $queryBuilder->setMaxResults(1); 
  }
  //#endregion
}