<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Tree\Traits\NestedSetEntityUuid;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Classe Task (Entité)
 * 
 * Représente une tâche
 * 
 * @category Entity
 * @package App\Entity
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
#[ApiResource(
  paginationEnabled: true,
  paginationClientItemsPerPage: true,
)]
#[ApiFilter(filterClass: RangeFilter::class, properties: ['createdAt'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['createdAt', 'title'], arguments: ['orderParameterName' => 'order'])]
#[Gedmo\Tree(type: 'nested')]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
  //#region Traits
  /**
   * Trait TimestampableEntity
   * 
   * Ce trait permet de gérer les dates de création et
   * de mise à jour d'une entité
   * 
   * @see TimestampableEntity
   */
  use TimestampableEntity;

  /**
   * Trait NestedSetEntityUuid
   * 
   * Permet de rendre la classe utilisable avec
   * le comportement Nested Set
   * 
   * @see NestedSetEntityUuid
   */
  use NestedSetEntityUuid;
  //#endregion

  //#region Propriétés
  /**
   * Propriété id
   * 
   * Identifiant de la tâche (UUID)
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Uuid|null $id Identifiant de la tâche
   */
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
  #[Groups(groups: ['task:read', 'task:write'])]
  private ?Uuid $id = null;

  /**
   * Propriété title
   * 
   * Titre de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $title Titre de la tâche
   */
  #[ORM\Column(type: Types::STRING, length: 255)]
  #[Assert\NotBlank(message: 'validation.task.title.not_blank')]
  #[Assert\Length(
    min: 3,
    max: 255,
    maxMessage: 'validation.task.title.length.max',
    minMessage: 'validation.task.title.length.min'
  )]
  #[Groups(groups: ['task:read', 'task:write'])]
  private ?string $title = null;

  /**
   * Propriété description
   * 
   * Description de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $description Description de la tâche
   */
  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[Assert\Length(
    max: 2048,
    maxMessage: 'validation.task.description.length.max'
  )]
  #[Groups(groups: ['task:read', 'task:write'])]
  private ?string $description = null;

  /**
   * Propriété dueDate
   * 
   * Date d'échéance de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var DateTimeInterface|null $dueDate Date d'échéance de la tâche
   */
  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
  #[Assert\Type(
    type: DateTimeInterface::class,
    message: 'validation.task.due_date.type'
  )]
  #[Groups(groups: ['task:read', 'task:write'])]
  private ?DateTimeInterface $dueDate = null;

  /**
   * Propriété STATUS_TODO (constante)
   * 
   * Statut de la tâche "À faire"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string STATUS_TODO Statut de la tâche "À faire"
   */
  private const string STATUS_TODO = 'todo';

  /**
   * Propriété STATUS_IN_PROGRESS (constante)
   * 
   * Statut de la tâche "En cours"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string STATUS_IN_PROGRESS Statut de la tâche "En cours"
   */
  private const string STATUS_IN_PROGRESS = 'in_progress';

  /**
   * Propriété STATUS_DONE (constante)
   * 
   * Statut de la tâche "Terminée"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string STATUS_DONE Statut de la tâche "Terminée"
   */
  private const string STATUS_DONE = 'done';

  /**
   * Propriété STATUSES (constante)
   * 
   * Liste des statuts de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var array STATUSES Liste des statuts de la tâche
   */
  private const array STATUSES = [
    self::STATUS_TODO,
    self::STATUS_IN_PROGRESS,
    self::STATUS_DONE,
  ];

  /**
   * Propriété status
   * 
   * Statut de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string $status Statut de la tâche
   */
  #[ORM\Column(type: Types::STRING, length: 20)]
  #[Assert\Choice(choices: self::STATUSES, message: 'validation.task.status.choice')]
  #[Groups(groups: ['task:read', 'task:write'])]
  private string $status = self::STATUS_TODO;

  /**
   * Propriété PRIORITY_LOW (constante)
   * 
   * Priorité de la tâche "Basse"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string PRIORITY_LOW Priorité de la tâche "Basse"
   */
  private const string PRIORITY_LOW = 'low';

  /**
   * Propriété PRIORITY_MEDIUM (constante)
   * 
   * Priorité de la tâche "Moyenne"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string PRIORITY_MEDIUM Priorité de la tâche "Moyenne"
   */
  private const string PRIORITY_MEDIUM = 'medium';

  /**
   * Propriété PRIORITY_HIGH (constante)
   * 
   * Priorité de la tâche "Haute"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string PRIORITY_HIGH Priorité de la tâche "Haute"
   */
  private const string PRIORITY_HIGH = 'high';

  /**
   * Propriété PRIORITY_URGENT (constante)
   * 
   * Priorité de la tâche "Urgente"
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string PRIORITY_URGENT Priorité de la tâche "Urgente"
   */
  private const string PRIORITY_URGENT = 'urgent';

  /**
   * Propriété PRIORITIES (constante)
   * 
   * Liste des priorités de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var array PRIORITIES Liste des priorités de la tâche
   */
  private const array PRIORITIES = [
    self::PRIORITY_LOW,
    self::PRIORITY_MEDIUM,
    self::PRIORITY_HIGH,
    self::PRIORITY_URGENT,
  ];

  /**
   * Propriété priority
   * 
   * Priorité de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string $priority Priorité de la tâche
   */
  #[ORM\Column(type: Types::STRING, length: 10)]
  #[Assert\Choice(choices: self::PRIORITIES, message: 'validation.task.priority.choice')]
  #[Groups(groups: ['task:read', 'task:write'])]
  private string $priority = self::PRIORITY_MEDIUM;

  /**
   * Propriété parent
   * 
   * Parent de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Task|null $parent Parent de la tâche
   */
  #[Gedmo\TreeParent]
  #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
  private ?self $parent = null;

  /**
   * Propriété children
   * 
   * Enfants de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Collection $children Enfants de la tâche
   */
  #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
  #[Groups(groups: ['task:read'])]
  private Collection $children;

  /**
   * Propriété project
   * 
   * Projet de la tâche
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Project|null $project Projet de la tâche
   */
  #[ORM\ManyToOne(inversedBy: 'tasks')]
  private ?Project $project = null;
  //#endregion

  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise une nouvelle instance de la 
   * classe Task
   * 
   * @access public
   * @since 1.0.0
   */
  public function __construct()
  {
    $this->children = new ArrayCollection();
  }
  //#endregion

  //#region Méthodes
  /**
   * Méthode getId
   * 
   * Retourne l'identifiant de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Uuid|null Identifiant de la tâche
   */
  public function getId(): ?Uuid
  {
    return $this->id;
  }

  /**
   * Méthode getTitle
   * 
   * Retourne le titre de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return string|null Titre de la tâche
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Méthode setTitle
   * 
   * Définit le titre de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $title Titre de la tâche
   * 
   * @return static
   */
  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Méthode getDescription
   * 
   * Retourne la description de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return string|null Description de la tâche
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Méthode setDescription
   * 
   * Définit la description de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $description Description de la tâche
   * 
   * @return static
   */
  public function setDescription(?string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Méthode getDueDate
   * 
   * Retourne la date d'échéance de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return DateTimeInterface|null Date d'échéance de la tâche
   */
  public function getDueDate(): ?DateTimeInterface
  {
    return $this->dueDate;
  }

  /**
   * Méthode setDueDate
   * 
   * Définit la date d'échéance de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param DateTimeInterface|null $dueDate Date d'échéance de la tâche
   * 
   * @return static
   */
  public function setDueDate(?DateTimeInterface $dueDate): static
  {
    $this->dueDate = $dueDate;

    return $this;
  }

  /**
   * Méthode getStatus
   * 
   * Retourne le statut de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return string Statut de la tâche
   */
  public function getStatus(): string
  {
    return $this->status;
  }

  /**
   * Méthode setStatus
   * 
   * Définit le statut de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $status Statut de la tâche
   * 
   * @return static
   */
  public function setStatus(string $status): static
  {
    if (!in_array(needle: $status, haystack: self::STATUSES)) {
      throw new InvalidArgumentException(message: 'Invalid status');
    }

    $this->status = $status;

    return $this;
  }

  /**
   * Méthode getPriority
   * 
   * Retourne la priorité de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return string Priorité de la tâche
   */
  public function getPriority(): string
  {
    return $this->priority;
  }

  /**
   * Méthode setPriority
   * 
   * Définit la priorité de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $priority Priorité de la tâche
   * 
   * @return static
   */
  public function setPriority(string $priority): static
  {
    if (!in_array(needle: $priority, haystack: self::PRIORITIES)) {
      throw new InvalidArgumentException(message: 'Invalid priority');
    }

    $this->priority = $priority;

    return $this;
  }

  /**
   * Méthode getParent
   * 
   * Retourne le parent de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Task|null Parent de la tâche
   */
  public function getParent(): ?self
  {
    return $this->parent;
  }

  /**
   * Méthode setParent
   * 
   * Définit le parent de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Task|null $parent Parent de la tâche
   * 
   * @return static
   */
  public function setParent(?self $parent): static
  {
    $this->parent = $parent;

    return $this;
  }

  /**
   * Méthode getchildren
   * 
   * Retourne les enfants de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Collection Enfants de la tâche
   */
  public function getChildren(): Collection
  {
    return $this->children;
  }

  /**
   * Méthode addChild
   * 
   * Ajoute un enfant à la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Task $child Enfant à ajouter
   * 
   * @return static
   */
  public function addChild(self $child): static
  {
    if (!$this->children->contains(element: $child)) {
      $this->children[] = $child;
      $child->setParent(parent: $this);
    }

    return $this;
  }

  /**
   * Méthode removeChild
   * 
   * Supprime un enfant de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Task $child Enfant à supprimer
   * 
   * @return static
   */
  public function removeChild(self $child): static
  {
    if ($this->children->removeElement(element: $child)) {
      $child->setParent(parent: null);
    }

    return $this;
  }

  /**
   * Méthode getProject
   * 
   * Retourne le projet de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Project|null Projet de la tâche
   */
  public function getProject(): ?Project
  {
    return $this->project;
  }

  /**
   * Méthode setProject
   * 
   * Définit le projet de la tâche
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Project|null $project Projet de la tâche
   * 
   * @return static
   */
  public function setProject(?Project $project): static
  {
    $this->project = $project;

    return $this;
  }

  /**
   * Méthode isRoot
   * 
   * Vérifie si la tâche est une racine
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return bool Vrai si la tâche est une racine, sinon faux
   */
  public function isRoot(): bool
  {
    return $this->parent === null;
  }

  /**
   * Méthode isLeaf
   * 
   * Vérifie si la tâche est une feuille
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return bool Vrai si la tâche est une feuille, sinon faux
   */
  public function isLeaf(): bool
  {
    return $this->children->isEmpty();
  }
  //#endregion
}
