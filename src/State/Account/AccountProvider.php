<?php

namespace App\State\Account;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class AccountProvider implements ProviderInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le provider du compte
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Security $security Sécurité
   */
  public function __construct(
    private readonly Security $security
  ) {}
  //#endregion

  //#region Méthodes
  /**
   * Méthode provide
   * 
   * Fournit le compte de l'utilisateur courant
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Operation $operation Opération
   * @param array $uriVariables Variables d'URI
   * @param array $context Contexte
   * 
   * @return User Compte de l'utilisateur courant
   */
  public function provide(
    Operation $operation, 
    array $uriVariables = [], 
    array $context = []
  ): ?User
  {
    return $this->security->getUser();
  }
}