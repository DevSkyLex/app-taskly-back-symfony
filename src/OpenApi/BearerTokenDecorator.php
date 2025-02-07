<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

/**
 * Classe BearerTokenDecorator (Décorateur)
 * @final
 * 
 * Permet de modifier le schéma de sécurité
 * 
 * @category OpenApi
 * @package App\OpenApi
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
#[AsDecorator(decorates: 'api_platform.openapi.factory')]
final class BearerTokenDecorator implements OpenApiFactoryInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Intialise les propriétés de la classe
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param OpenApiFactoryInterface $decorated
   */
  public function __construct(
    private readonly OpenApiFactoryInterface $decorated
  ) {
  }
  //#endregion

  //#region Méthodes
  /**
   * Méthode __invoke
   * 
   * Permet de modifier le schéma de sécurité
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param array $context
   * 
   * @return OpenApi Le schéma de sécurité modifié
   */
  public function __invoke(array $context = []): OpenApi
  {
    $openApi = ($this->decorated)($context);

    $components = $openApi->getComponents();
    $securitySchemes = $components->getSecuritySchemes() ?? new ArrayObject();

    $securitySchemes['Bearer'] = new ArrayObject(array: [
      'type' => 'http',
      'scheme' => 'bearer',
      'bearerFormat' => 'JWT',
      'description' => 'Enter the JWT Token',
    ]);

    $components = $components->withSecuritySchemes(securitySchemes: $securitySchemes);
    $openApi = $openApi->withComponents(components: $components);

    return $openApi;
  }
  //#endregion
}