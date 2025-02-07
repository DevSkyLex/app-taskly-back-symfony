<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Uid\Uuid;

/**
 * Classe UserRegistrationOutput
 * @final
 * 
 * Cette classe permet de représenter les données
 * renvoyées lors de l'inscription d'un utilisateur
 * 
 * @version 1.0.0
 */
final class UserRegistrationOutput
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise les données de l'inscription
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Uuid $id Identifiant de l'utilisateur
   * @param string $email Adresse email de l'utilisateur
   */
  public function __construct(
    /**
     * Propriété id
     * 
     * Identifiant de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var Uuid $id Identifiant de l'utilisateur
     */
    #[ApiProperty(
      description: 'User identifier',
      example: '550e8400-e29b-41d4-a716-446655440000',
      identifier: true,
      required: true,
    )]
    public Uuid $id,

    /**
     * Propriété email
     * 
     * Adresse email de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string $email Adresse email de l'utilisateur
     */
    #[ApiProperty(
      description: 'User email',
      example: 'example@domain.com',
      required: true,
    )]
    public string $email,
  ) {}
  //#endregion
}