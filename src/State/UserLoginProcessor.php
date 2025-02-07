<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\UserLoginOutput;
use App\Entity\User;
use App\Exception\InvalidCredentialsException;
use App\Repository\UserRepository;
use App\Services\RefreshTokenService;
use DateTimeImmutable;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserLoginProcessor implements ProcessorInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le processeur de connexion d'un 
   * utilisateur
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param JWTTokenManagerInterface $JWTManager
   * @param UserRepository $userRepository
   * @param UserPasswordHasherInterface $passwordHasher
   * @param SerializerInterface $serializer
   * @param RefreshTokenService $refreshTokenService
   */
  public function __construct(
    private readonly JWTTokenManagerInterface $JWTManager,
    private readonly UserRepository $userRepository,
    private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly SerializerInterface $serializer,
    private readonly RefreshTokenService $refreshTokenService
  ) {}
  //#endregion

  //#region Méthodes
  /**
   * Méthode process
   * 
   * Permet de traiter les données de connexion
   * d'un utilisateur
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param mixed $data Données d'inscription
   * @param Operation $operation Opération
   * @param array<string, mixed> $uriVariables Variables d'URI
   * @param array<string, mixed> $context Contexte
   * 
   * @return mixed Données traitées
   */
  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): JsonResponse {
    // Recherche l'utilisateur par son adresse email dans la base de données
    $user = $this->userRepository->findOneBy(criteria: ['email' => $data->email]);

    // Vérifie si l'utilisateur existe
    if (
      !$user instanceof PasswordAuthenticatedUserInterface && 
      !$user instanceof UserInterface
    ) {
      throw new InvalidCredentialsException(
        message: 'Invalid credentials'
      );
    }


    // Vérifie si le mot de passe est correct
    if (!$this->passwordHasher->isPasswordValid(
      user: $user, 
      plainPassword: $data->password
    )) {
      throw new InvalidCredentialsException(
        message: 'Invalid credentials'
      );
    }

    // Crée un token JWT pour l'utilisateur
    $token = $this->JWTManager->create($user);

    // Générer un token de rafraîchissement
    $refreshToken = $this->refreshTokenService->create(user: $user);

    // Enregistre le token de rafraîchissement
    $this->refreshTokenService->save(refreshToken: $refreshToken);

    // Récupère le token de rafraîchissement
    $refreshToken = $refreshToken->getRefreshToken();
    
    // Crée une réponse JSON
    $response = new JsonResponse();
    $response->headers->set(
      key: 'Content-Type', 
      values: 'application/ld+json'
    );

    /**
     * Cookie de rafraîchissement
     * 
     * Si le cookie de rafraîchissement est activé,
     * on crée un cookie pour le token de rafraîchissement
     * et on l'envoie dans la réponse
     * 
     * @var Cookie $cookie
     */
    if ($this->refreshTokenService->hasRefreshCookie()) {
      $cookie = $this->refreshTokenService->createRefreshCookie(
        refreshToken: $refreshToken
      );
      
      $response->headers->setCookie(
        cookie: $cookie
      );
    }

    /**
     * Crée un objet UserLoginOutput
     * 
     * Permet de retourner les données
     * de connexion de l'utilisateur
     * 
     * @var UserLoginOutput $output
     */

    $output = new UserLoginOutput(
      token: $token,
      refreshToken: $refreshToken,
    );

    /**
     * Sérialise l'objet UserLoginOutput
     * 
     * Permet de sérialiser l'objet UserLoginOutput
     * et de retourner les données de connexion
     * de l'utilisateur
     * 
     * @var string $json
     */
    $json = $this->serializer->serialize(
      data: $output,
      format: 'jsonld',
      context: $context
    );

    // Définit le contenu de la réponse
    $response->setContent(content: $json);

    return $response;
  }
  //#endregion
}