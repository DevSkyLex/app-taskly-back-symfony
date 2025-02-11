<?php

namespace App\State\Account;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Account\AccountUpdateAvatarInput;
use App\Entity\User;
use App\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vich\UploaderBundle\Handler\UploadHandler;

/**
 * Classe AccountUpdateAvatarProcessor
 * @final
 * 
 * Processeur de la mise à jour de l'avatar 
 * du compte
 * 
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final readonly class AccountUpdateAvatarProcessor implements ProcessorInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le processeur de l'avatar du compte
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param UploadHandler $uploadHandler Gestionnaire de téléchargement
   * @param RequestStack $requestStack Pile de requêtes
   * @param UserRepository $userRepository Dépôt des utilisateurs
   * @param Security $security Sécurité
   */
  public function __construct(
    private readonly UploadHandler $uploadHandler,
    private readonly RequestStack $requestStack,
    private readonly UserRepository $userRepository,
    private readonly Security $security,
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

    if (!$data instanceof AccountUpdateAvatarInput || !$data->avatarFile instanceof UploadedFile) {
       throw new BadRequestHttpException(message: 'Invalid file upload.');
    }

    $user->setAvatarFile(avatarFile: $data->avatarFile);
    $this->uploadHandler->upload($user, 'avatarFile');

    $this->userRepository->save(user: $user);

    return $user;
  }
  //#endregion
}