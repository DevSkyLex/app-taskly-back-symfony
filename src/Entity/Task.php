<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\TaskRepository;
use App\State\Task\TaskCreationProcessor;
use App\State\Task\TaskProvider;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Tree\Traits\NestedSetEntityUuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Enum\TaskPriority;
use App\Entity\Enum\TaskStatus;

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
  shortName: 'Task',
  paginationClientEnabled: true,
  paginationClientItemsPerPage: true,
  normalizationContext: ['groups' => ['task:read']],
  denormalizationContext: ['groups' => ['task:write']],
  uriVariables: [
    'project' => new Link(
      fromClass: Project::class,
      toProperty: 'project'
    ),
  ],
  operations: [
    new GetCollection(
      uriTemplate: '/projects/{project}/tasks',
      input: false,
      output: Task::class,
      provider: TaskProvider::class,
      openapi: new Operation(
        summary: 'List project tasks',
        description: 'Get the list of tasks for a specific project'
      )
    ),
    new Post(
      uriTemplate: '/projects/{project}/tasks',
      input: Task::class,
      processor: TaskCreationProcessor::class,
      openapi: new Operation(
        summary: 'Create a task',
        description: 'Allow a user to create a task'
      )
    ),
  ]
)]
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
  #[Groups(groups: ['task:read'])]
  #[ApiProperty(identifier: true)]
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
  #[Assert\NotBlank(message: 'The title is required')]
  #[Assert\Length(
    min: 3,
    max: 255,
    maxMessage: 'The title must be less than {{ limit }} characters long',
    minMessage: 'The title must be at least {{ limit }} characters long'
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
    min: 3,
    max: 2048,
    maxMessage: 'The description must be less than {{ limit }} characters long',
    minMessage: 'The description must be at least {{ limit }} characters long'
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
    message: 'The due date must be a valid date'
  )]
  #[Groups(groups: ['task:read', 'task:write'])]
  private ?DateTimeInterface $dueDate = null;

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
  #[ORM\Column(type: Types::STRING, length: 20, enumType: TaskStatus::class)]
  #[Groups(groups: ['task:read', 'task:write'])]
  private TaskStatus $status = TaskStatus::TODO;

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
  #[ORM\Column(type: Types::STRING, length: 10, enumType: TaskPriority::class)]
  #[Groups(groups: ['task:read', 'task:write'])]
  private TaskPriority $priority = TaskPriority::MEDIUM;

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
  #[ApiProperty(readableLink: false, writableLink: false)]
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
  #[ApiProperty(readableLink: false)]
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
  #[ORM\ManyToOne(inversedBy: 'tasks', targetEntity: Project::class)]
  #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
  #[Groups(groups: ['task:read'])]
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
   * @return TaskStatus Statut de la tâche
   */
  public function getStatus(): string|TaskStatus   
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
  public function setStatus(TaskStatus $status): static
  {
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
   * @return TaskPriority Priorité de la tâche
   */
  public function getPriority(): string|TaskPriority
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
   * @param TaskPriority $priority Priorité de la tâche
   * 
   * @return static
   */
  public function setPriority(TaskPriority $priority): static
  {
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
