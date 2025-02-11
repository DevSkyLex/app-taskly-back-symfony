<?php

namespace App\State\Account;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Account\AccountUpdateInput;
use App\Entity\User;
use App\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class AccountUpdateProcessor implements ProcessorInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le processeur de mise 
   * à jour du compte
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param UserRepository $userRepository Dépôt des utilisateurs
   * @param Security $security Sécurité
   */
  public function __construct(
    private readonly UserRepository $userRepository,
    private readonly Security $security
  ) {}
  //#endregion

  //#region Méthodes
    /**
   * Méthodes process
   * 
   * Traite la demande de mise à jour de l'avatar 
   * du compte
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param mixed $data Données
   * @param Operation $operation Opération
   * @param array $uriVariables Variables d'URI
   * @param array $context Contexte
   * 
   * @return User Utilisateur
   */
  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): User {
    $user = $this->security->getUser();

    if (!$user instanceof User) {
      throw new LogicException(message: 'The user is not authenticated.');
    }

    if (!$data instanceof AccountUpdateInput) {
      throw new BadRequestHttpException(message: 'Invalid data.');
    }

    if ($data->firstName) {
      $user->setFirstName(firstName: $data->firstName);
    }

    if ($data->lastName) {
      $user->setLastName(lastName: $data->lastName);
    }

    $this->userRepository->save(user: $user);

    return $user;
  }
  //#endregion
}