<?php

namespace App\Encoder;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Classe MultipartDecoder
 * @final 
 * 
 * Cette classe permet de décoder les données 
 * multipart
 * 
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class MultipartDecoder implements DecoderInterface
{
  //#region Constantes
  /**
   * Constante FORMAT
   * 
   * Format de décodage
   * 
   * @access public
   * @since 1.0.0
   * 
   * @var string FORMAT Format de décodage
   */
  public const string FORMAT = 'multipart';
  //#endregion

  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le décodeur multipart
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param RequestStack $requestStack Gestionnaire de requêtes
   */
  public function __construct(
    private readonly RequestStack $requestStack
  ) {}
  //#endregion

  //#region Méthodes
  /**
   * Méthode decode
   * 
   * Décodage des données multipart
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $data Données à décoder
   * @param string $format Format de décodage
   * @param array $context Contexte de décodage
   * 
   * @return array|null Données décodées
   */
  public function decode(
    string $data, 
    string $format, 
    array $context = []
  ): ?array {
    $request = $this->requestStack->getCurrentRequest();

    if (!$request) {
      return null;
    }

    return array_map(callback: static function (string $element): mixed {
      return json_decode(
        json: $element,
        associative: true,
        flags: JSON_THROW_ON_ERROR
      );
    }, array: $request->request->all()) + $request->files->all();
  }

  /**
   * Méthode supportsDecoding
   * 
   * Vérifie si le format de décodage est supporté
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $format Format de décodage
   * 
   * @return bool Vrai si le format est supporté, faux sinon
   */
  public function supportsDecoding(string $format): bool
  {
    return self::FORMAT === $format;
  }
  //#endregion
}