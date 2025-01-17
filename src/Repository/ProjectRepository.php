<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

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
   * @access private
   * @since 1.0.0
   * 
   * @var string ALIAS Alias de la table principale
   */
  private const string ALIAS = 'p';

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
  /**
   * Méthode findAllPaginated
   * 
   * Permet de récupérer la liste des projets paginée
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param int $limit Le nombre de projets par page
   * @param int $offset Le numéro de la page
   * @param string|null $search La recherche
   * @param array|null $filters Les filtres
   * 
   * @return Paginator<Project> La liste des projets paginée
   */
  public function findAllPaginated(
    int $limit = 10,
    int $offset = 0,
    string $search = null,
  ): Paginator {
    $queryBuilder = $this->createQueryBuilder(alias: self::ALIAS)
      ->orderBy(sort: 'p.createdAt', order: 'DESC')
      ->setMaxResults(maxResults: $limit)
      ->setFirstResult(firstResult: $offset);

    $criteria = Criteria::create();

    if ($search) {
      foreach (self::SEARCHABLE_FIELDS as $field) {
        $criteria->orWhere(
          expression: Criteria::expr()->contains(
            field: $field, 
            value: $search
          )
        );
      }
    }

    if (!empty($filters)) {
      foreach ($filters as $field => $value) {
        $criteria->andWhere(
          expression: Criteria::expr()->eq(
            field: $field, 
            value: $value
          )
        );
      }
    }

    $queryBuilder->addCriteria(criteria: $criteria);

    return new Paginator(
      query: $queryBuilder->getQuery(),
      fetchJoinCollection: true
    );
  }
  //#endregion
}
