<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTime;

/**
 * Classe TaskFixtures (Fixtures)
 * 
 * Classe permettant de charger des données fictives
 * dans la base de données
 * 
 * @category DataFixtures
 * @package App\DataFixtures
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class TaskFixtures extends Fixture implements DependentFixtureInterface
{
  //#region Constantes
  /**
   * Constante TASK_REFERENCE
   * 
   * Référence des tâches
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var string TASK_REFERENCE Référence des tâches
   */
  public const string TASK_REFERENCE = 'task_%d';

  /**
   * Constante NB_TASKS
   * 
   * Nombre de tâches à créer
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var int NB_TASKS Nombre de tâches à créer
   */
  public const int NB_TASKS = 10;

  /**
   * Constante NB_SUBTASKS
   * 
   * Nombre de sous-tâches à créer
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var int NB_SUBTASKS Nombre de sous-tâches à créer
   */
  public const int NB_SUBTASKS = 5;
  //#endregion

  //#region Méthodes
  /**
   * Méthode load
   * 
   * Méthode permettant de charger des données fictives
   * dans la base de données
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param ObjectManager $manager Gestionnaire d'entités
   * 
   * @return void Ne retourne rien
   */
  public function load(ObjectManager $manager): void
  {
    // Création des tâches
    for ($i = 0; $i < self::NB_TASKS; $i++) {
      $task = new Task();

      $task->setTitle(title: "Tâche n°$i")
           ->setDescription(description: "Description de la tâche n°$i");

      $project = $this->getReference(
        sprintf(self::TASK_REFERENCE, $i % ProjectFixtures::NB_PROJECTS),
        Project::class
      );

      $task->setProject(project: $project);

      for ($j = 0; $j < self::NB_SUBTASKS; $j++) {
        $subtask = new Task();

        $subtask->setTitle(title: "Sous-tâche n°$j")
                ->setDescription(description: "Description de la sous-tâche n°$j")
                ->setParent(parent: $task)
                ->setProject(project: $project);

        $manager->persist(object: $subtask);
      }

      $manager->persist(object: $task);
    }

    // Sauvegarde des données
    $manager->flush();
  }

  /**
   * Méthode getDependencies
   * 
   * Méthode permettant de charger les dépendances
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return array Tableau contenant les dépendances
   */
  public function getDependencies(): array
  {
    return [ProjectFixtures::class];
  }
  //#endregion
}