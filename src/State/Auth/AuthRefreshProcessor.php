<?php 

namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Services\RefreshTokenService;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\Auth\AuthLoginOutput;

final class AuthRefreshProcessor implements ProcessorInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le processeur de rafraîchissement
   * du token JWT
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param UserProviderInterface $userProvider
   * @param RefreshTokenManagerInterface $refreshTokenManager
   * @param RefreshTokenService $refreshTokenService
   * @param JWTTokenManagerInterface $JWTManager
   * @param RequestStack $requestStack
   * @param SerializerInterface $serializer
   */
  public function __construct(
    private readonly UserProviderInterface $userProvider,
    private readonly RefreshTokenManagerInterface $refreshTokenManager,
    private readonly RefreshTokenService $refreshTokenService,
    private readonly JWTTokenManagerInterface $JWTManager,
    private readonly RequestStack $requestStack,
    private readonly SerializerInterface $serializer
  ) {}
  //#endregion

  //#region Méthodes
  /**
   * Méthode process
   * 
   * Permet de traiter les données de rafraîchissement
   * du token JWT
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param mixed $data Données de rafraîchissement
   * @param Operation $operation Opération
   * @param array<string, mixed> $uriVariables Variables d'URI
   * @param array<string, mixed> $context Contexte
   * 
   * @return JsonResponse Réponse JSON
   */
  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): JsonResponse {
    /**
     * Récupération de la requête 
     * courante
     * 
     * @var Request $request
     */
    $request = $this->requestStack->getCurrentRequest();

    /**
     * Récupération du token de 
     * rafraîchissement
     * 
     * @var RefreshTokenInterface|null $refreshToken
     */
    $refreshToken = $this->refreshTokenService->getRefreshTokenFromCookie(
      request: $request
    );

    /**
     * Vérification de l'existence
     * du token de rafraîchissement
     * 
     * @throws BadRequestException
     */
    if (!$refreshToken) {
      throw new BadRequestException(
        message: 'No refresh token provided'
      );
    }

    /**
     * Validation du token de 
     * rafraîchissement
     * 
     * @throws BadRequestException
     */
    if (!$this->refreshTokenService->validate(refreshToken: $refreshToken)) {
      throw new BadRequestException(
        message: 'Invalid refresh token',
      );
    }

    /**
     * Récupération de l'utilisateur grâce
     * à l'identifiant du token de rafraîchissement
     * 
     * @var UserInterface $user
     */
    $user = $this->userProvider->loadUserByIdentifier(
      identifier: $refreshToken->getUsername()
    );

    if (!$user) {
      throw new BadRequestException(
        message: 'User not found'
      );
    }

    $token = $this->JWTManager->create($user);

    $this->refreshTokenService->delete(refreshToken: $refreshToken);
    $refreshToken = $this->refreshTokenService->create(user: $user);

    $response = new JsonResponse();
    $response->headers->set(
      key: 'Content-Type',
      values: 'application/ld+json'
    );

    if ($this->refreshTokenService->hasRefreshCookie()) {
      $cookie = $this->refreshTokenService->createRefreshCookie(
        refreshToken: $refreshToken
      );

      $response->headers->setCookie(
        cookie: $cookie
      );
    }

    $output = new AuthLoginOutput(
      token: $token,
      refreshToken: $refreshToken,
    );

    $json = $this->serializer->serialize(
      data: $output,
      format: 'jsonld',
      context: $context
    );

    $response->setContent(content: $json);

    return $response;
  }
  //#endregion
}