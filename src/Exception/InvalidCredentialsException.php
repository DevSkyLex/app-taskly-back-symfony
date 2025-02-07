<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * Classe InvalidCredentialsException
 * 
 * 
 * Exception levée lorsque les identifiants
 * sont invalides
 * 
 * @category Exception
 * @package App\Exception
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class InvalidCredentialsException extends BadRequestHttpException
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le message d'erreur
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $message Message d'erreur
   * @param Throwable $previous Exception précédente

   * @param int $code Code d'erreur
   * @param array $headers Headers de la réponse
   */
  public function __construct(
    string $message = 'Invalid credentials',
    Throwable $previous = null,
    int $code = 0,
    array $headers = []
  ) {
    parent::__construct(
      message: $message, 
      previous: $previous, 
      code: $code, 
      headers: $headers
    );
  }
  //#endregion
}