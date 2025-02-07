<?php

namespace App\State\Auth;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Auth\AuthRegistrationOutput;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Classe UserRegistrationProcessor
 * @final
 * 
 * Cette classe permet de traiter les données
 * d'inscription d'un utilisateur
 * 
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class AuthRegistrationProcessor implements ProcessorInterface
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise le processeur d'inscription 
   * d'un utilisateur
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param UserRepository $userRepository
   * @param UserPasswordHasherInterface $passwordHasher
   * @param SerializerInterface $serializer
   */
  public function __construct(
    private readonly UserRepository $userRepository,
    private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly SerializerInterface $serializer,
  ) {}
  //#endregion

  //#region Méthodes
  /**
   * Méthode process
   * 
   * Permet de traiter les données d'inscription
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
    // Vérification de l'existence de l'utilisateur
    $user = $this->userRepository->findOneBy(
      criteria: ['email' => $data->email]
    );

    if ($user) {
      throw new BadRequestException(message: 'This email is already used');
    }

    $user = new User();
    $user->setEmail(email: $data->email);
    $user->setPassword(
      password: $this->passwordHasher->hashPassword(
        user: $user, 
        plainPassword: $data->password
      )
    );

    $this->userRepository->save(user: $user);

    $output = new AuthRegistrationOutput(
      id: $user->getId(),
      email: $user->getEmail(),
    );

    $json = $this->serializer->serialize(
      data: $output,
      format: 'jsonld',
      context: $context
    );

    $response = new JsonResponse();
    $response->headers->set(
      key: 'Content-Type',
      values: 'application/ld+json'
    );

    $response->setContent(content: $json);

    return $response;
  }
  //#endregion
}