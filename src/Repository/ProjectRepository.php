<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Uid\Uuid;

/**
 * Classe ProjectRepository (Dépôt de données)
 * @final
 * 
 * Dépôt de données pour les projets
 * 
 * @category Repository
 * @package App\Repository
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class ProjectRepository extends ServiceEntityRepository
{
  //#region Constantes
  /**
   * Constante ALIAS
   * 
   * Alias de la table principale
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var string ALIAS Alias de la table principale
   */
  public const string ALIAS = 'p';

  /**
   * Constante SEARCHABLE_FIELDS
   * 
   * Champs de recherche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var array SEARCHABLE_FIELDS Champs de recherche
   */
  private const array SEARCHABLE_FIELDS = [
    'name',
    'description'
  ];

  /**
   * Constante FILTERABLE_FIELDS
   * 
   * Champs filtrables
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var array FILTERABLE_FIELDS Champs filtrables
   */
  private const array FILTERABLE_FIELDS = [
    'status'
  ];
  //#endregion

  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise une nouvelle instance de la classe
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param ManagerRegistry $registry Le registre de gestion
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct(
      registry: $registry,
      entityClass: Project::class
    );
  }
  //#endregion

  //#region Méthodes
  public function findProjectsForUser(User $user): array
  {
    return $this->createQueryBuilder(alias: self::ALIAS)
      ->select(self::ALIAS)
      ->join(join: self::ALIAS . '.members', alias: 'm')
      ->where('m.member = :userId')
      ->setParameter(key: 'userId', value: $user->getId())
      ->getQuery()
      ->getResult();
  }

  public function save(Project $project): void
  {
    $this->getEntityManager()->persist(object: $project);
    $this->getEntityManager()->flush();
  }

  public function delete(Project $project): void
  {
    $this->getEntityManager()->remove(object: $project);
    $this->getEntityManager()->flush();
  }

  public function exists(Uuid $id): bool
  {
    return $this->createQueryBuilder(alias: self::ALIAS)
      ->select('COUNT(p.id)')
      ->where('p.id = :id')
      ->setParameter(key: 'id', value: $id)
      ->getQuery()
      ->getSingleScalarResult() > 0;
  }

  public function isMember(Uuid $projectId, Uuid $userId): bool
  {
    $qb = $this->createQueryBuilder('p');

    return (bool) $qb
      ->select('1')
      ->innerJoin('p.members', 'm')
      ->where('m.member = :userId')
      ->andWhere('m.project = :projectId')
      ->setParameter('userId', $userId)
      ->setParameter('projectId', $projectId)
      ->getQuery()
      ->getOneOrNullResult();
  }


  //#endregion
}
