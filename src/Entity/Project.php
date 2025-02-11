<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Enum\ProjectRole;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Gedmo\Timestampable\Traits\TimestampableEntity;
/**
 * Classe Project (Entité)
 * 
 * Représente un Projet
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
  normalizationContext: ['groups' => ['project:read']],
  denormalizationContext: ['groups' => ['project:write']],
  outputFormats: ['jsonld' => ['application/ld+json']],
  inputFormats: [
    'jsonld' => ['application/ld+json'],
    'json' => ['application/json'],
  ],
)]
#[ApiFilter(filterClass: RangeFilter::class, properties: ['createdAt'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial'])]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
class Project
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
   * Trait SoftDeleteableEntity
   * 
   * Ce trait permet de gérer la suppression 
   * logique d'une entité
   * 
   * @see SoftDeleteableEntity
   */
  use SoftDeleteableEntity;
  //#endregion

  //#region Propriétés
  /**
   * Propriété id
   * 
   * Identifiant du projet
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Uuid|null $id Identifiant du projet
   */
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
  #[Groups(groups: ['project:read', 'project:write'])]
  private ?Uuid $id = null;

  /**
   * Propriété name
   * 
   * Nom du projet
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $name Nom du projet
   */
  #[ORM\Column(type: Types::STRING, length: 255)]
  #[Groups(groups: ['project:read', 'project:write'])]
  private ?string $name = null;

  /**
   * Propriété description
   * 
   * Description du projet
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $description Description du projet
   */
  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[Groups(groups: ['project:read', 'project:write'])]
  private ?string $description = null;

  /**
   * Propriété tasks
   * 
   * Liste des tâches du projet
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Collection $tasks Liste des tâches du projet
   */
  #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project')]
  #[Groups(groups: ['project:read'])]
  private Collection $tasks;


  /**
   * Propriété members
   * 
   * Liste des membres du projet
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var Collection $members Liste des membres du projet
   */
  #[ORM\OneToMany(targetEntity: ProjectMember::class, mappedBy: 'project')]
  #[Groups(groups: ['project:read'])]
  private Collection $members;
  //#endregion

  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise une nouvelle instance de la 
   * classe Project
   * 
   * @access public
   * @since 1.0.0
   */
  public function __construct()
  {
    $this->tasks = new ArrayCollection();
    $this->members = new ArrayCollection();
  }
  //#endregion

  //#region Méthodes
  /**
   * Méthode getId
   * 
   * Retourne l'identifiant du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Uuid|null Identifiant du projet
   */
  public function getId(): ?Uuid
  {
    return $this->id;
  }

  /**
   * Méthode getName
   * 
   * Retourne le nom du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return string|null Nom du projet
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Méthode setName
   * 
   * Définit le nom du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $name Nom du projet
   * 
   * @return Project Instance de la classe Project
   */
  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Méthode getDescription
   * 
   * Retourne la description du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return string|null Description du projet
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Méthode setDescription
   * 
   * Définit la description du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $description Description du projet
   * 
   * @return Project Instance de la classe Project
   */
  public function setDescription(?string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Méthode getTasks
   * 
   * Retourne la liste des tâches du projet
   * 
   * La méthode vérifie que la tâche n'est pas une "sous-tâche"
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Collection Liste des tâches du projet
   */
  public function getTasks(): Collection
  {
    $tasks = $this->tasks->filter(p: function (Task $task): bool {
      return $task->isRoot();
    })->toArray();

    return new ArrayCollection(elements: array_values(array: $tasks));
  }

  /**
   * Méthode addTask
   * 
   * Ajoute une tâche au projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Task $task Tâche à ajouter
   * 
   * @return Project Instance de la classe Project
   */
  public function addTask(Task $task): static
  {
    if (!$this->tasks->contains(element: $task)) {
      $this->tasks->add(element: $task);
      $task->setProject(project: $this);
    }

    return $this;
  }

  /**
   * Méthode removeTask
   * 
   * Supprime une tâche du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Task $task Tâche à supprimer
   * 
   * @return Project Instance de la classe Project
   */
  public function removeTask(Task $task): static
  {
    if ($this->tasks->removeElement(element: $task)) {
      if ($task->getProject() === $this) {
        $task->setProject(project: null);
      }
    }

    return $this;
  }

  /**
   * Méthode getMembers
   * 
   * Retourne la liste des membres du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return Collection Liste des membres du projet
   */
  public function getMembers(): Collection
  {
    return $this->members;
  }

  /**
   * Méthode addMember
   * 
   * Ajoute un membre au projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param ProjectMember $member Membre à ajouter
   * 
   * @return Project Instance de la classe Project
   */
  public function addMember(ProjectMember $member): static
  {
    if (!$this->members->contains($member)) {
      $this->members->add($member);
      $member->setProject($this);
    }

    return $this;
  }

  /**
   * Méthode removeMember
   * 
   * Supprime un membre du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param ProjectMember $member Membre à supprimer
   * 
   * @return Project Instance de la classe Project
   */
  public function removeMember(ProjectMember $member): static
  {
    if ($this->members->removeElement($member)) {
      if ($member->getProject() === $this) {
        $member->setProject(null);
      }
    }

    return $this;
  }

  /**
   * Méthode isMember
   * 
   * Vérifie si un utilisateur est membre du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param User $user Utilisateur à vérifier
   * 
   * @return bool Renvoie vrai si l'utilisateur est membre du projet, sinon faux
   */
  public function isMember(User $user): bool {
    foreach ($this->getMembers() as $member) {
      if ($member->getMember() === $user) {
        return true;
      }
    }

    return false;
  }

  /**
   * Méthode isManager
   * 
   * Vérifie si un utilisateur est manager du projet
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param User $user Utilisateur à vérifier
   * 
   * @return bool Renvoie vrai si l'utilisateur est manager du projet, sinon faux
   */
  public function isManager(User $user): bool {
    foreach ($this->getMembers() as $member) {
      if ($member->getMember() === $user && $member->getRole() === ProjectRole::MANAGER) {
        return true;
      }
    }

    return false;
  }

  public function hasRole(User $user, array $roles): bool
  {
    foreach ($this->getMembers() as $member) {
      if ($member->getMember() === $user && in_array(
        $member->getRole(), 
        $roles
      )) { return true; }
    }

    return false;
  }
  //#endregion
}
