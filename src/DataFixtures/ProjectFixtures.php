<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Project;

/**
 * Classe ProjectFixtures (Fixtures)
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
final class ProjectFixtures extends Fixture
{
  //#region Constantes
  /**
   * Constante PROJECT_REFERENCE
   * 
   * Référence des projets
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var string PROJECT_REFERENCE Référence des projets
   */
  public const string PROJECT_REFERENCE = 'task_%d';

  /**
   * Constante NB_PROJECT
   * 
   * Nombre de projets à créer
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var int NB_PROJECT Nombre de projets à créer
   */
  public const int NB_PROJECTS = 5;
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
    for ($i = 0; $i < self::NB_PROJECTS; $i++) {
      $project = new Project();
      $project->setName(name: "Projet $i")
              ->setDescription(description: "Description du projet $i");

      $manager->persist(object: $project);

      $this->addReference(name: sprintf(
        self::PROJECT_REFERENCE, 
        $i
      ), object: $project);
    }

    $manager->flush();
  }
  //#endregion
}