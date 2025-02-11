<?php

namespace App\DTO\Account;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe AccountUpdateInput
 * @final
 * 
 * Cette classe permet de représenter les données
 * nécessaires à la mise à jour d'un compte 
 * utilisateur
 * 
 * @version 1.0.0
 */
final class AccountUpdateInput
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise les données de mise à jour du compte
   * 
   * @access public
   * @since 1.0.0
   */
  public function __construct(
    /**
     * Propriété firstName
     * 
     * Prénom de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string|null $firstName Prénom de l'utilisateur
     */
    #[ApiProperty(
      description: 'First name of the user',
      example: 'John',
      required: false,
    )]
    #[Assert\Length(
      min: 2,
      max: 50,
      minMessage: 'The first name must be at least {{ limit }} characters long',
      maxMessage: 'The first name cannot be longer than {{ limit }} characters'
    )]
    #[Assert\NotBlank(
      message: 'Please enter a first name',
    )]
    #[Groups(['user:write'])]
    public ?string $firstName = null,

    /**
     * Propriété lastName
     * 
     * Nom de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string|null $lastName Nom de l'utilisateur
     */
    #[ApiProperty(
      description: 'Last name of the user',
      example: 'Doe',
      required: false,
    )]
    #[Assert\Length(
      min: 2,
      max: 50,
      minMessage: 'The last name must be at least {{ limit }} characters long',
      maxMessage: 'The last name cannot be longer than {{ limit }} characters'
    )]
    #[Assert\NotBlank(
      message: 'Please enter a last name',
    )]
    #[Groups(['user:write'])]
    public ?string $lastName = null,
  ) {}
  //#endregion
}