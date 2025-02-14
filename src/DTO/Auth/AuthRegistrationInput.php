<?php

namespace App\DTO\Auth;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe AuthRegistrationInput
 * @final
 * 
 * Cette classe permet de représenter les données
 * nécessaires à l'inscription d'un utilisateur
 * 
 * @version 1.0.0
 */
final class AuthRegistrationInput
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise les données d'inscription
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $email Adresse email de l'utilisateur
   * @param string $password Mot de passe de l'utilisateur
   */
  public function __construct(
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
      example: 'user@domain.com',
      required: true,
    )]
    #[Assert\Email(
      message: 'The email "{{ value }}" is not a valid email.',
    )]
    #[Assert\NotBlank(
      message: 'Email is required',
    )]
    public string $email = '',

    /**
     * Propriété password
     * 
     * Mot de passe de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string $password Mot de passe de l'utilisateur
     */
    #[ApiProperty(
      description: 'User password',
      example: 'securePassword',
      required: true
    )] 
    #[Assert\Length(
      min: 8,
      max: 200,
      minMessage: 'Your password must be at least {{ limit }} characters long',
      maxMessage: 'Your password cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotBlank(
      message: 'Password is required'
    )]
    public string $password = '',

    /**
     * Propriété firstName
     * 
     * Prénom de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string $firstName Prénom de l'utilisateur
     */
    #[ApiProperty(
      description: 'User first name',
      example: 'John',
      required: true,
    )]
    #[Assert\Length(
      min: 2,
      max: 50,
      minMessage: 'The first name must be at least {{ limit }} characters long',
      maxMessage: 'The first name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotBlank(
      message: 'First name is required',
    )]
    public string $firstName = '',

    /**
     * Propriété lastName
     * 
     * Nom de l'utilisateur
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string $lastName Nom de l'utilisateur
     */
    #[ApiProperty(
      description: 'User last name',
      example: 'Doe',
      required: true,
    )]
    #[Assert\Length(
      min: 2,
      max: 50,
      minMessage: 'The last name must be at least {{ limit }} characters long',
      maxMessage: 'The last name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotBlank(
      message: 'Last name is required',
    )]
    public string $lastName = '',
  ) {}
  //#endregion
}