<?php

namespace App\Services;

use DateTimeImmutable;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class RefreshTokenService 
{
  //#region Propriétés
  /**
   * Propriété refreshTtl
   * 
   * Temps de vie du token de 
   * rafraîchissement
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var int $refreshTtl
   */
  private static int $refreshTtl = 604800;


  /**
   * Propriété singleUse
   * 
   * Indique si le token de 
   * rafraîchissement est utilisé 
   * qu'une fois
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var bool $singleUse
   */
  private static bool $singleUse = true;

  /**
   * Propriété returnExpirationParameterName
   * 
   * Nom du paramètre de 
   * retour de l'expiration du 
   * token de rafraîchissement
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string $returnExpirationParameterName
   */
  private static string $returnExpirationParameterName = 'refresh_expiration';

  /**
   * Propriété cookieName
   * 
   * Nom du cookie de 
   * rafraîchissement
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string $cookieName
   */
  private static string $cookieName = 'refresh_token';


  /**
   * Propriété cookieEnabled
   * 
   * Indique si le cookie de 
   * rafraîchissement est activé
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var bool $cookieEnabled
   */
  private static bool $cookieEnabled = true;

  /**
   * Propriété cookieSameSite
   * 
   * Indique le site du cookie de 
   * rafraîchissement
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string $cookieSameSite
   */
  private static string $cookieSameSite = 'lax';

  /**
   * Propriété $cookiePartitioned
   * 
   * Indique que le cookie doit être partitionné
   *
   * @access public
   * @since 1.0.0
   * 
   * @var bool $cookiePartitioned
   */
  private static bool $cookiePartitioned = true;

  /**
   * Propriété cookieSecure
   * 
   * Indique si le cookie de 
   * rafraîchissement est sécurisé
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var bool $cookieSecure
   */
  private static bool $cookieSecure = false;

  /**
   * Propriété cookiePath
   * 
   * Chemin du cookie de 
   * rafraîchissement
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string $cookiePath
   */
  private static string $cookiePath = '/';

  /**
   * Propriété cookieDomain
   * 
   * Domaine du cookie de 
   * rafraîchissement
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var string|null $cookieDomain
   */
  private static ?string $cookieDomain = null;

  /**
   * Propriété cookieHttpOnly
   * 
   * Indique si le cookie de 
   * rafraîchissement est accessible 
   * uniquement par le serveur
   * 
   * @access private
   * @since 1.0.0
   * 
   * @var bool $cookieHttpOnly
   */
  private static bool $cookieHttpOnly = true;
  //#endregion

  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le service de token de 
   * rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param RefreshTokenManagerInterface $manager
   * @param RefreshTokenGeneratorInterface $generator
   */
  public function __construct(
    private readonly ParameterBagInterface $parameterBag,
    private readonly RefreshTokenManagerInterface $manager,
    private readonly RefreshTokenGeneratorInterface $generator,
  ) { $this->initialize(); }
  //#endregion

  //#region Méthodes
  /**
   * Méthode initialize
   * 
   * Initialise les propriétés du service
   * 
   * @access private
   * @since 1.0.0
   * 
   * @return void Ne retourne rien
   */
  private function initialize(): void
  {
    self::$refreshTtl = $this->parameterBag->get(name: 'gesdinet_jwt_refresh_token.ttl');
    self::$singleUse = $this->parameterBag->get(name: 'gesdinet_jwt_refresh_token.single_use');
    self::$returnExpirationParameterName = $this->parameterBag->get(name: 'gesdinet_jwt_refresh_token.return_expiration_parameter_name');

    $cookie = $this->parameterBag->get(name: 'gesdinet_jwt_refresh_token.cookie');
    self::$cookieEnabled = $cookie['enabled'];
    self::$cookiePath = $cookie['path'];
    self::$cookieDomain = $cookie['domain'];
    self::$cookieSecure = $cookie['secure'];
    self::$cookieHttpOnly = $cookie['http_only'];
    self::$cookieSameSite = $cookie['same_site'];
    self::$cookiePartitioned = $cookie['partitioned'];
  }

  /**
   * Méthode create
   * 
   * Crée un token de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param UserInterface $user Utilisateur
   * 
   * @return RefreshTokenInterface Le token de rafraîchissement
   */
  public function create(UserInterface $user): RefreshTokenInterface
  {
    return $this->generator->createForUserWithTtl(
      user: $user, 
      ttl: self::$refreshTtl
    );
  }

  /**
   * Méthode save
   * 
   * Enregistre un token de 
   * rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param RefreshTokenInterface $refreshToken Token de rafraîchissement
   * 
   * @return void Ne retourne rien
   */
  public function save(RefreshTokenInterface $refreshToken): void
  {
    $this->manager->save($refreshToken);
  }


  /**
   * Méthode delete
   * 
   * Supprime un token de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param RefreshTokenInterface $refreshToken Token de rafraîchissement
   * 
   * @return void Ne retourne rien
   */
  public function delete(RefreshTokenInterface $refreshToken): void
  {
    $this->manager->delete($refreshToken);
  }

  /**
   * Méthode get
   * 
   * Récupère un token de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $refreshToken Token de rafraîchissement
   * 
   * @return RefreshTokenInterface|null Le token de rafraîchissement
   */
  public function get(string $refreshToken): ?RefreshTokenInterface
  {
    return $this->manager->get($refreshToken);
  }


  /**
   * Méthode createRefreshCookie
   * 
   * Crée un cookie de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $refreshToken
   * 
   * @return Cookie Le cookie de rafraîchissement
   */
  public function createRefreshCookie(string $refreshToken): Cookie
  {
    $refreshTtl = self::$refreshTtl;

    return new Cookie(
      name: self::$cookieName,
      value: $refreshToken,
      expire: new DateTimeImmutable(datetime: "+$refreshTtl seconds"),
      path: self::$cookiePath,
      domain: self::$cookieDomain,
      secure: self::$cookieSecure,
      httpOnly: self::$cookieHttpOnly,
      sameSite: self::$cookieSameSite,
      partitioned: self::$cookiePartitioned,
    );
  }

  /**
   * Méthode getRefreshTokenFromCookie
   * 
   * Récupère le token de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param Request $request Requête
   * 
   * @return string|null Le token de rafraîchissement
   */
  public function getRefreshTokenFromCookie(Request $request): ?RefreshTokenInterface
  {
    $refreshToken = $request->cookies->get(key: self::$cookieName);

    if (!$refreshToken || !is_string(value: $refreshToken)) {
      return null;
    }

    $token = $this->get(refreshToken: $refreshToken);

    return $token;
  }

  /**
   * Méthode validate
   * 
   * Valide un token de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param string $refreshToken Token de rafraîchissement
   * 
   * @return bool Vrai si le token est valide, sinon faux
   */
  public function validate(string|RefreshTokenInterface $refreshToken): bool
  {
    if (is_string(value: $refreshToken)) {
      $refreshToken = $this->get(refreshToken: $refreshToken);
    }

    if (!$refreshToken) {
      return false;
    }

    return $refreshToken->isValid();
  }

  /**
   * Méthode hasRefreshCookie
   * 
   * Vérifie si le cookie de rafraîchissement
   * 
   * @access public
   * @since 1.0.0
   * 
   * @return bool Vrai si le cookie de rafraîchissement existe, sinon faux
   */
  public function hasRefreshCookie(): bool
  {
    return self::$cookieEnabled;
  }
  //#endregion

}