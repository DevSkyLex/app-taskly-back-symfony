<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Classe JWTExceptionListener
 * @final
 * 
 * Permet de gérer les exceptions liées au JWT
 * 
 * @category EventListener
 * @package App\EventListener
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class JWTExceptionListener
{
  //#region Méthodes
  /**
   * Méthode onJWTNotFound
   * 
   * Permet de gérer l'événement 
   * lexik_jwt_authentication.on_jwt_not_found
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param JWTNotFoundEvent $event L'événement
   * 
   * @return void Ne retourne rien
   */
  #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_not_found')]
  public function onJWTNotFound(JWTNotFoundEvent $event): void
  {
    $response = new JsonResponse(data: [
      '@context'    => '/api/contexts/Error',
      '@type'       => 'JWTError',
      'title'       => 'JWT Token not found',
      'detail'      => 'You must provide a valid JWT token', 
      'description' => 'You must provide a valid JWT token',
      'status'      => Response::HTTP_UNAUTHORIZED,
    ], status: Response::HTTP_UNAUTHORIZED);

    $response->headers->set(
      key: 'Content-Type', 
      values: 'application/ld+json'
    );

    $event->setResponse(response: $response);
  }

  /**
   * Méthode onJWTInvalid
   * 
   * Permet de gérer l'événement 
   * lexik_jwt_authentication.on_jwt_invalid
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param JWTInvalidEvent $event L'événement
   * 
   * @return void Ne retourne rien
   */
  #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_invalid')]
  public function onJWTInvalid(JWTInvalidEvent $event): void
  {
    $response = new JsonResponse(data: [
      '@context'    => '/api/contexts/Error',
      '@type'       => 'JWTError',
      'title'       => 'JWT Token is invalid',
      'detail'      => 'You must provide a valid JWT token',
      'description' => 'You must provide a valid JWT token',
      'status'      => Response::HTTP_UNAUTHORIZED,
    ], status: Response::HTTP_UNAUTHORIZED);

    $response->headers->set(
      key: 'Content-Type', 
      values: 'application/ld+json'
    );

    $event->setResponse(response: $response);
  }

  /**
   * Méthode onJWTExpired
   * 
   * Permet de gérer l'événement 
   * lexik_jwt_authentication.on_jwt_expired
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param JWTExpiredEvent $event L'événement
   * 
   * @return void Ne retourne rien
   */
  #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_expired')]
  public function onJWTExpired(JWTExpiredEvent $event): void
  {
    $response = new JsonResponse(data: [
      '@context'    => '/api/contexts/Error',
      '@type'       => 'JWTError',
      'title'       => 'JWT Token is expired',
      'detail'      => 'You must provide a valid JWT token',
      'description' => 'You must provide a valid JWT token',
      'status'      => Response::HTTP_FORBIDDEN,
    ], status: Response::HTTP_FORBIDDEN);

    $response->headers->set(
      key: 'Content-Type', 
      values: 'application/ld+json'
    );

    $event->setResponse(response: $response);
  }
  //#endregion
}
