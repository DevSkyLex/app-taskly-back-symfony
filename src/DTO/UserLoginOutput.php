<?php 

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;

/**
 * Classe UserLoginOutput
 * @final
 * 
 * Cette classe permet de représenter les données
 * renvoyées lors de la connexion d'un utilisateur
 * 
 * @version 1.0.0
 */
final class UserLoginOutput
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise les données de connexion
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $token Jeton d'authentification
   * @param string $refreshToken Jeton de rafraîchissement
   */
  public function __construct(
    /**
     * Propriété token
     * 
     * Jeton d'authentification
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string $token Jeton d'authentification
     */
    #[ApiProperty(
      description: 'Authentication token',
      example: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
      required: true,
    )]
    public string $token,

    /**
     * Propriété refreshToken
     * 
     * Jeton de rafraîchissement
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var string $refreshToken Jeton de rafraîchissement
     */
    #[ApiProperty(
      description: 'Refresh token',
      example: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
      required: true,
    )]
    public string $refreshToken,
  ) {}
  //#endregion
}